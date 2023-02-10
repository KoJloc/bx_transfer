<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRest;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    private $entitiesByEmployerId = [];

    public $taskFilter = [];

    public $leadActivityOwnersId = [];
    public $anotherActivityOwnersId = [];

    function __construct()
    {
        parent::__construct();
    }

    public function __invoke(Request $request)
    {
//---------------------------------Хук--------------------------------------------------------------------------------//

        define('C_REST_WEB_HOOK_URL', 'https://xn--24-9kc.xn--d1ao9c.xn--p1ai/rest/53083/b4ye1avz6dmkned1/');

//----------------------------------Реквест-выбраных-людей------------------------------------------------------------//

        $this->markedPeople = $request['Departments'];
        $this->markedActivePeople = $request['onlyActiveDepartments'];

//--------------------------------------Получаем-сущности-------------------------------------------------------------//

        $leads = CRest::firstBatch('crm.lead.list', [
            'filter' => ['ASSIGNED_BY_ID' => $this->markedPeople],
            'select' => [
                'ID',
                'ASSIGNED_BY_ID',
            ]
        ]);

        $deals = CRest::firstBatch('crm.deal.list', [
            'filter' => ['ASSIGNED_BY_ID' => $this->markedPeople],
            'select' => [
                'ID',
                'ASSIGNED_BY_ID',
            ]
        ]);

        $contacts = CRest::firstBatch('crm.contact.list', [
            'filter' => ['ASSIGNED_BY_ID' => $this->markedPeople],
            'select' => [
                'ID',
                'ASSIGNED_BY_ID',
            ]
        ]);

//--------------------------------------Обрабатываем-сущности---------------------------------------------------------//
        //Перебор массива лидов
        foreach ($leads as $lead) {
            $this->entitiesByEmployerId['leads'][$lead['ID']] = $lead['ASSIGNED_BY_ID'];
            //Создаем фильтр для task'ов лидов
            $this->taskFilter['UF_CRM_TASK'][] = 'L_' . $lead['ID'];
            //Создаем фильтр для активити
            $this->leadActivityOwnersId[] = $lead['ID'];
        }

        //Перебор массива сделок
        foreach ($deals as $deal) {
            $this->entitiesByEmployerId['deals'][$deal['ID']] = $deal['ASSIGNED_BY_ID'];
            //Создаем фильтр для task'ов сделок
            $this->taskFilter['UF_CRM_TASK'][] = 'D_' . $deal['ID'];
            //Создаем фильтр для активити
            $this->anotherActivityOwnersId[] = $deal['ID'];
        }
        //Перебор массива контактов
        foreach ($contacts as $contact) {
            $this->entitiesByEmployerId['contacts'][$contact['ID']] = $contact['ASSIGNED_BY_ID'];
            //Создаем фильтр для task'ов контактов
            $this->taskFilter['UF_CRM_TASK'][] = 'C_' . $contact['ID'];
            //Создаем фильтр для активити
            $this->anotherActivityOwnersId[] = $contact['ID'];
        }

//--------------------------------------------------------Таски-------------------------------------------------------//

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
            if ($task['ufCrmTask'][0] == 0) continue;

            $ufCrmTask = $task['ufCrmTask'][0];
            $clearedTaskFilter = substr($ufCrmTask, 2);
            $typeName = 'leads';

            if (str_contains($ufCrmTask, 'D')) {
                $typeName = 'deals';
            } elseif (str_contains($ufCrmTask, 'C')) {
                $typeName = 'contacts';
            }
            $this->entitiesByEmployerId[$typeName][$clearedTaskFilter]['tasks'][] = $task['id'];
//            $this->entitiesByEmployerId[$typeName][$clearedTaskFilter]['tasks'][$task['id']][] = $task['responsibleId'];
        }

//----------------------------------------------------Активити--------------------------------------------------------//


        $leadActivityFilter = array(
            'OWNER_ID' => $this->leadActivityOwnersId,
            'COMPLETED' => 'N',
            "PROVIDER_ID" => ['VOXIMPLANT_CALL', 'CRM_MEETING'], //'VOXIMPLANT_CALL' 'CRM_MEETING' 'TASKS'
        );

        $anotherActivityFilter = array(
            'OWNER_ID' => $this->anotherActivityOwnersId,
            'COMPLETED' => 'N',
            "PROVIDER_ID" => ['VOXIMPLANT_CALL', 'CRM_MEETING'], //'VOXIMPLANT_CALL' 'CRM_MEETING' 'TASKS'
            'RESPONSIBLE_ID' => $this->markedPeople,
        );

        $leadActivities = CRest::firstBatch('crm.activity.list', [
            'FILTER' => $leadActivityFilter,
        ]);

        dd($leadActivities);

        foreach ($leadActivities as $activity) {
            $this->entitiesByEmployerId['leads'][$activity['OWNER_ID']]['activity'][] = $activity['ID'];
        }

        $anotherActivities = CRest::firstBatch('crm.activity.list', [
            'FILTER' => $anotherActivityFilter,
        ]);

        foreach ($anotherActivities as $activity) {
            $this->entitiesByEmployerId[$activity['TYPE_ID'] == 2 ? 'deals' : 'contacts'][$activity['OWNER_ID']]['activity'][] = $activity['ID'];
        }

//        dump(count($this->entitiesByEmployerId['leads']));
//        dump(count($this->entitiesByEmployerId['deals']));
//        dump(count($this->entitiesByEmployerId['contacts']));


//        return [
//          'leads' => $leads,
//            'entitiesByEmployerId' => $this->entitiesByEmployerId,
//            'activePeopleMultiSelectFromLeads' => $this->activePeopleMultiSelect,
//            'peopleMultiSelectFromLeads' => $this->peopleMultiSelect
//            'activePeopleMultiSelect' => $activePeopleMultiSelect,
//            'peopleMultiSelect' => $peopleMultiSelect,
//        ];
    }
}
