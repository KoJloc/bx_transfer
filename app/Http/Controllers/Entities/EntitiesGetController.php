<?php

namespace App\Http\Controllers\Entities;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRest;
use Illuminate\Http\Request;

class EntitiesGetController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    protected $taskFilter = [];
    protected $activityOwnersId = [];
    protected $clearedTaskFilter = [];
    protected $responseWay = 0;

    public function vueGet(Request $request)
    {
        $this->get($request);
    }

    public function get($request, ...$users)
    {
        $entitiesUpdateController = new EntitiesUpdateController();
        if (!empty($request['Departments'])) {
            define('C_REST_WEB_HOOK_URL', 'https://xn--24-9kc.xn--d1ao9c.xn--p1ai/rest/53083/b4ye1avz6dmkned1/');
            $this->markedPeople = $request['Departments'];
            info('VUE(toUsers):', [$this->markedPeople]);
        } elseif (!empty($users)) {
            $this->markedPeople = $users[0][0];
            $this->responseWay = 1;
            info('API(fromUsers):', [$this->markedPeople]);
        } else {
            return 'Неверные входные данные';
        }
        //Получаем лиды из Битрикса
        $leads = CRest::firstBatch('crm.lead.list', [
            'filter' => [
                'ASSIGNED_BY_ID' => $this->markedPeople,
                '!STATUS_ID' => 'CONVERTED',
            ],
            'select' => [
                'ID',
                'ASSIGNED_BY_ID',
            ]
        ]);
        //Получаем сделки из Битрикса
        $deals = CRest::firstBatch('crm.deal.list', [
            'filter' => [
                'ASSIGNED_BY_ID' => $this->markedPeople,
                '!STAGE_ID' => ['C6:NEW', 'C6:WON'],
            ],
            'select' => [
                'ID',
                'ASSIGNED_BY_ID',
            ]
        ]);
        //Получаем контакты из Битрикса
        $contacts = CRest::firstBatch('crm.contact.list', [
            'filter' => [
                'ASSIGNED_BY_ID' => $this->markedPeople
            ],
            'select' => [
                'ID',
                'ASSIGNED_BY_ID',
            ]
        ]);
//--------------------------------------Обрабатываем_сущности---------------------------------------------------------//
        //Перебор массива лидов
        foreach ($leads as $lead) {
            //Добавление лидов в общий массив
            $this->entitiesByEmployerId['leads'][$lead['ID']]['resp'] = $lead['ASSIGNED_BY_ID'];
            //Создаем фильтр для task'ов лидов
            $this->taskFilter['UF_CRM_TASK'][] = 'L_' . $lead['ID'];
            //Создаем фильтр для активити
            $this->activityOwnersId[] = $lead['ID'];
        }

        //Перебор массива сделок
        foreach ($deals as $deal) {
            //Добавление сделок в общий массив
            $this->entitiesByEmployerId['deals'][$deal['ID']]['resp'] = $deal['ASSIGNED_BY_ID'];
            //Создаем фильтр для task'ов сделок
            $this->taskFilter['UF_CRM_TASK'][] = 'D_' . $deal['ID'];
            //Создаем фильтр для активити
            $this->activityOwnersId[] = $deal['ID'];
        }

        //Перебор массива контактов
        foreach ($contacts as $contact) {
            //Добавление контактов в общий массив
            $this->entitiesByEmployerId['contacts'][$contact['ID']]['resp'] = $contact['ASSIGNED_BY_ID'];
            //Создаем фильтр для task'ов контактов
            $this->taskFilter['UF_CRM_TASK'][] = 'C_' . $contact['ID'];
            //Создаем фильтр для активити
            $this->activityOwnersId[] = $contact['ID'];
        }

//--------------------------------------------------------Таски-------------------------------------------------------//

        if ((empty($leads) && empty($deals) && empty($contacts))) return 'error';

        $tasks = CRest::firstBatch('tasks.task.list', [
            'filter' => [
                $this->taskFilter,
                'REAL_STATUS' => [1, 2, 3, 4, 6, 7]
            ],
            'select' => [
                'ID',
                'RESPONSIBLE_ID',
                'UF_CRM_TASK',
            ]
        ]);

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

        $activityFilter = [
            'OWNER_ID' => $this->activityOwnersId,
            'COMPLETED' => 'N',
            'PROVIDER_ID' => ['VOXIMPLANT_CALL', 'CRM_MEETING'], //'VOXIMPLANT_CALL' 'CRM_MEETING' 'TASKS'
            'RESPONSIBLE_ID' => $this->markedPeople,
        ];

        $activities = CRest::firstBatch('crm.activity.list', [
            'FILTER' => $activityFilter,
        ]);

        foreach ($activities as $activity) {
            $ternar = $activity['OWNER_TYPE_ID'] == 2 ? 'deals' : 'contacts';
            if ($activity['OWNER_TYPE_ID'] == 1) {
                $ternar = 'leads';
            }
            $this->entitiesByEmployerId[$ternar][$activity['OWNER_ID']]['activity'][$activity['ID']] = $activity['RESPONSIBLE_ID'];
        }

        $this->responseWay == 1
            ? $entitiesUpdateController->update($request, $this->entitiesByEmployerId, $users[1])
            : $entitiesUpdateController->update($request, $this->entitiesByEmployerId);
    }
}
