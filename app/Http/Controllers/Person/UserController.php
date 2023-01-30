<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRest;

class UserController extends Controller
{
    public function __invoke()
    {

        define('C_REST_WEB_HOOK_URL', 'https://xn--24-9kc.xn--d1ao9c.xn--p1ai/rest/53083/b4ye1avz6dmkned1/');//url on creat Webhook

        $people = CRest::firstBatch('user.get', [
            'FILTER' => [
                'USER_TYPE' => 'employee'
            ]
        ]);
        $selectedPeople = [];
        $peopleMultiSelect = [];
        $activePeopleMultiSelect = [];

        foreach ($people as $person) {
            $selectedPeople = [
                'id' => $person['ID'],
                'text' => $person['LAST_NAME'] . ' ' . $person['NAME'] . ' ' . $person['SECOND_NAME']
            ];
            if ($person['ACTIVE'] === true) {
                $activePeopleMultiSelect[] = $selectedPeople;
                $peopleMultiSelect[] = $selectedPeople;
            } else {
                $peopleMultiSelect[] = [
                    'id' => $person['ID'],
                    'text' => $person['LAST_NAME'] . ' ' . $person['NAME'] . ' ' . $person['SECOND_NAME']
                ];
            }
        }

        return [
//          'people' => count($people),
            'activePeopleMultiSelect' => $activePeopleMultiSelect,
            'peopleMultiSelect' => $peopleMultiSelect,
        ];
    }
}
