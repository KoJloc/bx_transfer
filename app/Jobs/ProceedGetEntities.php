<?php

namespace App\Jobs;

use App\Http\Controllers\Entities\EntitiesUpdateController;
use App\Http\Traits\CRest;
use App\Models\History;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProceedGetEntities implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CRest;

    protected $fromUsers, $toUsers, $response;

    public $tries = 3;
    public $backoff = 15;
    public $uniqueFor = 3600;
    public function uniqueId(): string
    {
        return $this->response->id;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($response, $fromUsers, $toUsers)
    {
        $this->response = $response;
        $this->fromUsers = $fromUsers;
        $this->toUsers = $toUsers;
    }

    public function handle()
    {
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        ignore_user_abort(true);

        $entitiesUpdateController = new EntitiesUpdateController();

        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/TransferInfo.log')
        ])->info('fromUsers:', $this->fromUsers['from']);

        $leads = CRest::firstBatch('crm.lead.list', [
            'filter' => [
                'ASSIGNED_BY_ID' => $this->fromUsers['from'],
                '!STATUS_ID' => ['CONVERTED', 'JUNK'],
            ],
            'select' => [
                'ID',
                'ASSIGNED_BY_ID',
            ],
        ]);
        $deals = CRest::firstBatch('crm.deal.list', [
            'filter' => [
                'ASSIGNED_BY_ID' => $this->fromUsers['from'],
                '!STAGE_ID' => ['C6:NEW', 'C6:WON'],
            ],
            'select' => [
                'ID',
                'ASSIGNED_BY_ID',
            ],
        ]);
        $contacts = CRest::firstBatch('crm.contact.list', [
            'filter' => [
                'ASSIGNED_BY_ID' => $this->fromUsers['from'],
            ],
            'select' => [
                'ID',
                'ASSIGNED_BY_ID',
            ],
        ]);
//--------------------------------------Обрабатываем_сущности---------------------------------------------------------//
        $this->leadActivityOwnersId = [];
        $this->otherActivityOwnersId = [];

        if (isset($leads)) {
            //Перебор массива лидов
            foreach ($leads as $lead) {
                //Добавление лидов в общий массив
                $this->entitiesByEmployerId['leads'][$lead['ID']]['resp'] = $lead['ASSIGNED_BY_ID'];
                //Создаем фильтр для task'ов лидов
                $this->taskFilter['UF_CRM_TASK'][] = 'L_' . $lead['ID'];
                //Создаем фильтр для активити
                $this->leadActivityOwnersId[] = $lead['ID'];
            }
        }

        if (isset($deals)) {
            //Перебор массива сделок
            foreach ($deals as $deal) {
                //Добавление сделок в общий массив
                $this->entitiesByEmployerId['deals'][$deal['ID']]['resp'] = $deal['ASSIGNED_BY_ID'];
                //Создаем фильтр для task'ов сделок
                $this->taskFilter['UF_CRM_TASK'][] = 'D_' . $deal['ID'];
                //Создаем фильтр для активити
                $this->otherActivityOwnersId[] = $deal['ID'];
            }
        }

        if (isset($contacts)) {
            //Перебор массива контактов
            foreach ($contacts as $contact) {
                //Добавление контактов в общий массив
                $this->entitiesByEmployerId['contacts'][$contact['ID']]['resp'] = $contact['ASSIGNED_BY_ID'];
                //Создаем фильтр для task'ов контактов
                $this->taskFilter['UF_CRM_TASK'][] = 'C_' . $contact['ID'];
                //Создаем фильтр для активити
                $this->otherActivityOwnersId[] = $contact['ID'];
            }
        }

//--------------------------------------------------------Таски-------------------------------------------------------//

        if ((empty($leads) && empty($deals) && empty($contacts))) return 'error';

        $tasks = [];
        $taskFilterChunks = array_chunk($this->taskFilter['UF_CRM_TASK'], 500);
        unset($this->taskFilter);

        foreach ($taskFilterChunks as $chunk) {
            $currentFilter['UF_CRM_TASK'] = $chunk;
            $tasksFromBitrix = CRest::firstBatch('tasks.task.list', [
                'filter' => [
                    $currentFilter,
                    'REAL_STATUS' => [1, 2, 3, 4, 6, 7]
                ],
                'select' => [
                    'ID',
                    'RESPONSIBLE_ID',
                    'UF_CRM_TASK',
                ]
            ]);
            if (!isset($tasksFromBitrix['tasks'])) {
                foreach ($tasksFromBitrix as $item) {
                    $tasks[] = $item;
                }
            }
        }

        foreach ($tasks as $task) {
            if (!empty($task['ufCrmTask'])) {
                $regular = '/[0-9]+/';
                foreach ($task['ufCrmTask'] as $ufCrmTask)
                    preg_match_all($regular, $ufCrmTask, $matches);
                $this->clearedTaskFilter = $matches[0][0];
                $typeName = 'leads';
                if (str_contains($ufCrmTask, 'D')) {
                    $typeName = 'deals';
                } elseif (str_contains($ufCrmTask, 'C')) {
                    $typeName = 'contacts';
                }
                $this->entitiesByEmployerId[$typeName][$this->clearedTaskFilter]['tasks'][$task['id']] = $task['responsibleId'];
            }
        }

//----------------------------------------------------Активити--------------------------------------------------------//
        $activities = [];
        if(count($this->leadActivityOwnersId) > 0) {
            $leadActivityOwnersIdChunks = array_chunk($this->leadActivityOwnersId, 500);
        }
        if(count($this->otherActivityOwnersId) > 0) {
            $otherActivityOwnersIdChunks = array_chunk($this->otherActivityOwnersId, 500);
        }
        unset($this->leadActivityOwnersId, $this->otherActivityOwnersId);

        if(isset($leadActivityOwnersIdChunks)) {
            foreach ($leadActivityOwnersIdChunks as $chunk) {
                $activityFilter = [
                    'OWNER_ID' => $chunk,
                    'COMPLETED' => 'N',
                    'PROVIDER_ID' => ['VOXIMPLANT_CALL', 'CRM_MEETING'], //'VOXIMPLANT_CALL' 'CRM_MEETING' 'TASKS'
                ];

                $activitiesFromBitrix = CRest::firstBatch('crm.activity.list', [
                    'FILTER' => $activityFilter,
                ]);

                if (!empty($activitiesFromBitrix)) {
                    foreach ($activitiesFromBitrix as $item) {
                        $activities[] = $item;
                    }
                }
            }
        }

        if(isset($otherActivityOwnersIdChunks)) {
            foreach ($otherActivityOwnersIdChunks as $chunk) {
                $activityFilter = [
                    'OWNER_ID' => $chunk,
                    'COMPLETED' => 'N',
                    'PROVIDER_ID' => ['VOXIMPLANT_CALL', 'CRM_MEETING'], //'VOXIMPLANT_CALL' 'CRM_MEETING' 'TASKS'
                    'RESPONSIBLE_ID' => $this->fromUsers['from'],
                ];

                $activitiesFromBitrix = CRest::firstBatch('crm.activity.list', [
                    'FILTER' => $activityFilter,
                ]);

                if (!empty($activitiesFromBitrix)) {
                    foreach ($activitiesFromBitrix as $item) {
                        $activities[] = $item;
                    }
                }
            }
        }

        foreach ($activities as $activity) {
            $ternar = $activity['OWNER_TYPE_ID'] == 2 ? 'deals' : 'contacts';
            if ($activity['OWNER_TYPE_ID'] == 1) {
                $ternar = 'leads';
            }
            $this->entitiesByEmployerId[$ternar][$activity['OWNER_ID']]['activity'][$activity['ID']] = $activity['RESPONSIBLE_ID'];
        }

        $entitiesUpdateController->update($this->entitiesByEmployerId, $this->toUsers, [], [], $filtred = false);

        return 'completed';
    }
}
