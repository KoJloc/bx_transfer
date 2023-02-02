<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRest;
use App\Models\Lead;

class LeadController extends Controller
{
    function __construct(){
        parent::__construct();
    }
    public function __invoke(Lead $lead)
    {
        define('C_REST_WEB_HOOK_URL', 'https://xn--24-9kc.xn--d1ao9c.xn--p1ai/rest/53083/b4ye1avz6dmkned1/');//url on creat Webhook

        $leads = CRest::call('crm.lead.list', [
            'filter' => ['ASSIGNED_BY_ID' => $this->peopleMultiSelect,],
            'select' => ['ID','ASSIGNED_BY_ID', 'CONTACT_ID', 'NAME', 'LEAD_SUMMARY', 'DATE_CREATE', 'LAST_NAME', 'SECOND_NAME', 'PHONE']
        ]);
//        $deals = CRest::firstBatch('crm.deal.list', [
//            'filter' => ['ASSIGNED_BY_ID' => $this->peopleMultiSelect,],
//            'select' => ['ID','ASSIGNED_BY_ID', 'CONTACT_ID', 'NAME', 'LEAD_SUMMARY', 'DATE_CREATE', 'LAST_NAME', 'SECOND_NAME', 'PHONE']
//        ]);
//
//        $contacts = CRest::firstBatch('crm.contacts.list', [
//            'filter' => ['ASSIGNED_BY_ID' => $this->peopleMultiSelect,],
//            'select' => ['ID','ASSIGNED_BY_ID', 'CONTACT_ID', 'NAME', 'LEAD_SUMMARY', 'DATE_CREATE', 'LAST_NAME', 'SECOND_NAME', 'PHONE']
//        ]);

        dd($this->peopleMultiSelect);

        foreach ($this->peopleMultiSelect as $person){
            $this->leadsById[$person] = $person;
            foreach ($leads as $key => $lead){
                if ($lead['ASSIGNED_BY_ID'] == $person){
                    $this->leadsById[$person][$key][] = $key;
                }
            }
        }


//        for ($i = 0; $i < count($this->peopleMultiSelect); $i++) {
//            array_push($this->leadsById['personId']=>$this->peopleMultiSelect[$i])
//            for ($j = 0; $j < count($leads); $j++) {
//                    if ($this->peopleMultiSelect[$i]['id'] == $lead[$j]['ASSIGNED_BY_ID']) {
//                        $this->leadsById=[
//                           'personId'=>$this->peopleMultiSelect[$i]['id'],
//                            'leadId'=>$lead[$j]['ID'],
//                        ];
//                    }
//                }
//
//            $selectedPeople = [
//                'id' => $person['ID'],
//                'text' => $person['LAST_NAME'] . ' ' . $person['NAME'] . ' ' . $person['SECOND_NAME']
//            ];
//            if ($person['ACTIVE'] === true) {
//                $activePeopleMultiSelect[] = $selectedPeople;
//                $peopleMultiSelect[] = $selectedPeople;
//            } else {
//                $peopleMultiSelect[] = [
//                    'id' => $person['ID'],
//                    'text' => $person['LAST_NAME'] . ' ' . $person['NAME'] . ' ' . $person['SECOND_NAME']
//                ];
//            }
//        }

        return [
//          'leads' => $leads,
            'leadsById' => $this->leadsById,
//            'activePeopleMultiSelectFromLeads' => $this->activePeopleMultiSelect,
//            'peopleMultiSelectFromLeads' => $this->peopleMultiSelect
//            'activePeopleMultiSelect' => $activePeopleMultiSelect,
//            'peopleMultiSelect' => $peopleMultiSelect,
        ];
    }
}
