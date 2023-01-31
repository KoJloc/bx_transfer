<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRest;
use App\Http\Resources\MarkedPeopleResource;

class LeadController extends Controller
{

    public function __invoke(MarkedPeopleResource $resource)
    {
        define('C_REST_WEB_HOOK_URL', 'https://xn--24-9kc.xn--d1ao9c.xn--p1ai/rest/53083/b4ye1avz6dmkned1/');//url on creat Webhook

        $markedPeopleMultiSelect = [];
        $markedOnlyActivePeopleMultiSelect = [];

        $leads = CRest::firstBatch('crm.lead.list', [
            'select' => ['ID','ASSIGNED_BY_ID', 'CONTACT_ID', 'NAME', 'LEAD_SUMMARY', 'DATE_CREATE', 'LAST_NAME', 'SECOND_NAME', 'PHONE']
        ]);

        $selectedPeople = [];
        $connectedLeads = [];

        foreach ($leads as $item) {

        }



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
          'leads' => $leads
//            'activePeopleMultiSelect' => $activePeopleMultiSelect,
//            'peopleMultiSelect' => $peopleMultiSelect,
        ];
    }
}
