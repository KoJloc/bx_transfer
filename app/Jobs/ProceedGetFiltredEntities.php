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

class ProceedGetFiltredEntities implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CRest;

    protected $response, $params, $fromUsers, $toUsers;

    public $tries = 1;

    public $uniqueFor = 3600;

    public function middleware()
    {
        return [new WithoutOverlapping($this->response->id)];
    }

    public function uniqueId(): string
    {
        return $this->response->id;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($response, $params, $fromUsers, $toUsers)
    {
        $this->response = $response;
        $this->params = $params;
        $this->fromUsers = $fromUsers;
        $this->toUsers = $toUsers;
    }

    private function SortArrayToIDs($array): array|string
    {
        $returnArray = [];
        if (isset($array[0]['id'])) {
            foreach ($array as $item) {
                $returnArray[] = $item['id'];
            }
            return $returnArray;
        } else {
            return 'error';
        }
    }

    private function SortArrayToStatus($array): array|string
    {
        $returnArray = [];
        if (isset($array[0]['status'])) {
            foreach ($array as $item) {
                $returnArray[] = $item['status'];
            }
            return $returnArray;
        } else {
            return 'error';
        }
    }

    private function ConvertDataTimeFormat($data): string
    {
        $data = date('Y-m-d', strtotime($data . '+1 days'));
        return date("Y-m-d", strtotime($data)) . "T00:00:00+05:00";
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
        ])->info('fromUsers:', $this->fromUsers);

        if (!isset($this->params['checkedType'])) return 'error';

        if (in_array($this->params['checkedType'], ['lead', 'all'])) {
            $leadsFilters = [
                'ASSIGNED_BY_ID' => $this->fromUsers, //Отвественный за лид
                array_key_exists('leadStatus', $this->params) ? 'STATUS_ID' : '!STATUS_ID' => array_key_exists('leadStatus', $this->params) ? $this->SortArrayToStatus($this->params['leadStatus']) : 'CONVERTED', //Статус лида
                'UF_CRM_1662639727' => array_key_exists('leadType', $this->params) ? $this->SortArrayToIDs($this->params['leadType']) : [], //Тип лида
                'UF_CRM_1622543817615' => array_key_exists('city', $this->params) ? $this->SortArrayToIDs($this->params['city']) : [], //Город
                'SOURCE_DESCRIPTION' => array_key_exists('aboutSource', $this->params) ? $this->SortArrayToIDs($this->params['aboutSource']) : [], //Дополнительно об источнике
                'UF_CRM_1626242852' => array_key_exists('regions', $this->params) ? $this->SortArrayToIDs($this->params['regions']) : [], //Регион
                'SOURCE_ID' => array_key_exists('sources', $this->params) ? $this->SortArrayToIDs($this->params['sources']) : [], //Отдел продаж
                'UF_CRM_1561882407' => array_key_exists('salesDepartments', $this->params) ? $this->SortArrayToIDs($this->params['salesDepartments']) : [], //Источник
                '>=DATE_CREATE' => array_key_exists('fromDate', $this->params) ? $this->ConvertDataTimeFormat($this->params['fromDate']) : [], //Начальная дата
                '<=DATE_CREATE' => array_key_exists('toDate', $this->params) ? $this->ConvertDataTimeFormat($this->params['toDate']) : [], //Конечная дата
            ];

            $leads = CRest::firstBatch('crm.lead.list', [
                'filter' => $leadsFilters,
                'select' => [
                    'ID',
                    'ASSIGNED_BY_ID',
                ],
            ]);
        }

        if (in_array($this->params['checkedType'], ['deal', 'all'])) {
            $dealsFilter = [
                'ASSIGNED_BY_ID' => $this->fromUsers, //Ответственный за сделку
                '!STAGE_ID' => ['C6:NEW', 'C6:WON'],
                'TYPE_ID' => array_key_exists('dealType', $this->params) ? $this->SortArrayToStatus($this->params['dealType']) : [], //Тип сделки
                'CATEGORY_ID' => array_key_exists('dealFunnel', $this->params) ? $this->SortArrayToIDs($this->params['dealFunnel']) : [], //Воронка сделки
                'UF_CRM_1626242852' => array_key_exists('regions', $this->params) ? $this->SortArrayToIDs($this->params['regions']) : [], //Регион
                'SOURCE_ID' => array_key_exists('sources', $this->params) ? $this->SortArrayToIDs($this->params['sources']) : [], //Отдел продаж
                'UF_CRM_1561882407' => array_key_exists('salesDepartments', $this->params) ? $this->SortArrayToIDs($this->params['salesDepartments']) : [], //Источник
                '>=DATE_CREATE' => array_key_exists('fromDate', $this->params) ? $this->ConvertDataTimeFormat($this->params['fromDate']) : [], //Начальная дата
                '<=DATE_CREATE' => array_key_exists('toDate', $this->params) ? $this->ConvertDataTimeFormat($this->params['toDate']) : [], //Конечная дата
            ];

            $deals = CRest::call('crm.deal.list', [
                'filter' => $dealsFilter,
                'select' => [
                    'ID',
                    'ASSIGNED_BY_ID',
                ],
            ]);
        }

        if (in_array($this->params['checkedType'], ['contact', 'all'])) {
            $contactsFilters = [
                'ASSIGNED_BY_ID' => $this->fromUsers, //Отвественный за контакт
                'SOURCE_ID' => array_key_exists('sources', $this->params) ? $this->SortArrayToIDs($this->params['sources']) : [], //Отдел продаж
                'UF_CRM_1561882407' => array_key_exists('salesDepartments', $this->params) ? $this->SortArrayToIDs($this->params['salesDepartments']) : [], //Источник
                '>=DATE_CREATE' => array_key_exists('fromDate', $this->params) ? $this->ConvertDataTimeFormat($this->params['fromDate']) : [], //Начальная дата
                '<=DATE_CREATE' => array_key_exists('toDate', $this->params) ? $this->ConvertDataTimeFormat($this->params['toDate']) : [], //Конечная дата
            ];

            $contacts = CRest::firstBatch('crm.contact.list', [
                'filter' => $contactsFilters,
                'select' => [
                    'ID',
                    'ASSIGNED_BY_ID',
                ],
            ]);
        }
//--------------------------------------Обрабатываем_сущности---------------------------------------------------------//
        if (isset($leads)) {
            foreach ($leads as $lead) {
                $this->entitiesByEmployerId['leads'][$lead['ID']]['resp'] = $lead['ASSIGNED_BY_ID'];
                $this->taskFilter['UF_CRM_TASK'][] = 'L_' . $lead['ID'];
                $this->activityOwnersId[] = $lead['ID'];
            }
        }

        if (isset($deals)) {
            foreach ($deals as $deal) {
                $this->entitiesByEmployerId['deals'][$deal['ID']]['resp'] = $deal['ASSIGNED_BY_ID'];
                $this->taskFilter['UF_CRM_TASK'][] = 'D_' . $deal['ID'];
                $this->activityOwnersId[] = $deal['ID'];
            }
        }

        if (isset($contacts)) {
            foreach ($contacts as $contact) {
                $this->entitiesByEmployerId['contacts'][$contact['ID']]['resp'] = $contact['ASSIGNED_BY_ID'];
                $this->taskFilter['UF_CRM_TASK'][] = 'C_' . $contact['ID'];
                $this->activityOwnersId[] = $contact['ID'];
            }
        }
//--------------------------------------------------------Таски-------------------------------------------------------//
        if ((empty($leads) && empty($deals) && empty($contacts))) return 'error';

        $tasks = [];
        $regular = '/[0-9]+/';
        $taskFilterChunks = array_chunk($this->taskFilter['UF_CRM_TASK'], 1000);
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
                foreach ($task['ufCrmTask'] as $ufCrmTask) {
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
        }
//----------------------------------------------------Активити--------------------------------------------------------//
        $activities = [];
        $activityOwnersIdChunks = array_chunk($this->activityOwnersId, 1000);
        unset($this->activityOwnersId);

        foreach ($activityOwnersIdChunks as $chunk) {
            $activityFilter = [
                'OWNER_ID' => $chunk,
                'COMPLETED' => 'N',
                'PROVIDER_ID' => ['VOXIMPLANT_CALL', 'CRM_MEETING'], //'VOXIMPLANT_CALL' 'CRM_MEETING' 'TASKS'
                'RESPONSIBLE_ID' => $this->fromUsers,
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

        foreach ($activities as $activity) {
            $ternar = $activity['OWNER_TYPE_ID'] == 2 ? 'deals' : 'contacts';
            if ($activity['OWNER_TYPE_ID'] == 1) {
                $ternar = 'leads';
            }
            $this->entitiesByEmployerId[$ternar][$activity['OWNER_ID']]['activity'][$activity['ID']] = $activity['RESPONSIBLE_ID'];
        }

        $entitiesUpdateController->update($this->entitiesByEmployerId, $this->toUsers, array_key_exists('newSource', $this->params) ? $this->params['newSource']['id'] : [],
                                                                                            array_key_exists('newSalesDepartment', $this->params) ? $this->params['newSalesDepartment']['id'] : [], $filtred = true);
        return 'completed';
    }

}
