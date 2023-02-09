<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRest;
use App\Http\Middleware\EncryptCookies;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    private $entitiesByEmployerId = [];

    public $taskFilter = [];

    function __construct(){
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
                'CONTACT_ID',
                'NAME',
                'LEAD_SUMMARY',
                'DATE_CREATE',
                'LAST_NAME',
                'SECOND_NAME',
                'PHONE'
            ]
        ]);

        $deals = CRest::firstBatch('crm.deal.list', [
            'filter' => ['ASSIGNED_BY_ID' => $this->markedPeople],
            'select' => [
                'ID',
                'ASSIGNED_BY_ID',
                'TITLE',
                'CONTACT_ID',
                'TYPE_ID',
                'OPPORTUNITY'
            ]
        ]);

        $contacts = CRest::firstBatch('crm.contact.list', [
            'filter' => ['ASSIGNED_BY_ID' => $this->markedPeople],
            'select' => [
                'ID',
                'ASSIGNED_BY_ID',
                'SECOND_NAME',
                'LAST_NAME',
                'LEAD_ID'
            ]
        ]);

//--------------------------------------Обрабатываем-сущности---------------------------------------------------------//

        //Создаем массив сотрудников
        foreach ($this->markedPeople as $person){
            $this->entitiesByEmployerId[$person] = [
                'leads' => [],
                'deals' => [],
                'contacts' => []
            ];
        }

//        dump($this->entitiesByEmployerId);

        //Перебор массива лидов
        foreach ($leads as $lead){
            // Привязываем лиды к сотрудникам
            $this->entitiesByEmployerId[$lead['ASSIGNED_BY_ID']]['leads'][$lead['ID']] = [];
            //Создаем фильтр для task'ов лидов
            $this->taskFilter['UF_CRM_TASK'][] = 'L_'.$lead['ID'];
        }

        //Перебор массива сделок
        foreach ($deals as $deal){
            // Привязываем сделки к сотрудникам
            $this->entitiesByEmployerId[$deal['ASSIGNED_BY_ID']]['deals'][$deal['ID']] = [];
            //Создаем фильтр для task'ов сделок
            $this->taskFilter['UF_CRM_TASK'][] = 'D_'.$deal['ID'];
        }
        //Перебор массива контактов
        foreach ($contacts as $contact){
            // Привязываем контакты к сотрудникам
            $this->entitiesByEmployerId[$contact['ASSIGNED_BY_ID']]['contacts'][$contact['ID']] = [];
            //Создаем фильтр для task'ов контактов
            $this->taskFilter['UF_CRM_TASK'][] = 'C_'.$contact['ID'];
        }


//        echo 'Лиды';
//        dump($leads)
//        echo 'Сделки';
//        dump($deals);
//        echo 'Контакты';
//        dump($contacts);
//        echo 'С кого передаем';
//        dump($this->markedPeople);
//        dump($this->entitiesByEmployerId);

//        dump($this->taskFilter);



//--------------------------------------------------------Таски-------------------------------------------------------//
//
//        $tasks = CRest::firstBatch('tasks.task.list', [
//            'filter' => $this->taskFilter,
//            'select' => [
//                'ID',
//                'RESPONSIBLE_ID',
//                'UF_CRM_TASK',
//            ]
//        ]);
//
//        foreach ($tasks as $task){
//            if ($task['ID'] ==  $this->entitiesByEmployerId[$contact['ASSIGNED_BY_ID']]);
//        }
//
//        dump($tasks);

//----------------------------------------------------Активити--------------------------------------------------------//

        $filter_activity = array(
            'OWNER_ID'       => $this->markedPeople,
//            'COMPLETED'      => 'N',
        );

        $activity = CRest::firstBatch('crm.activity.list', [
            'FILTER' => $filter_activity,
            'select' => [
//                'COMPLETED',
//                'ID',
//                'OWNER_ID',
//                'OWNER_TYPE_ID',
//                'ASSOCIATED_ENTITY_ID',
            ],
        ]);

        dd($activity);


//        foreach ($activity as $item) {
//            if ($item['OWNER_TYPE_ID'] == 1){
//                $this->entitiesByEmployerId[$item['OWNER_ID']]['leads'][$item['ASSOCIATED_ENTITY_ID']] = $item['ID'];
//            }elseif ($item['OWNER_TYPE_ID'] == 2) {
//                $this->entitiesByEmployerId[$item['OWNER_ID']]['deals'][$item['ASSOCIATED_ENTITY_ID']] = $item['ID'];
//            }elseif ($item['OWNER_TYPE_ID'] == 3) {
//                $this->entitiesByEmployerId[$item['OWNER_ID']]['deals'][$item['ASSOCIATED_ENTITY_ID']] = $item['ID'];
//            }
//        }

        echo 'После присвоения активити';
        dump($this->entitiesByEmployerId);

//        dump($activity);


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
