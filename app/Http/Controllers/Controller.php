<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\CRest;

class Controller extends BaseController
{
    public $peopleMultiSelect;
    public $activePeopleMultiSelect;
    public $markedLeadsById;
    public $leadsById;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
//        $allEntitiesByPersonId = [
//            'id' => [
//                'contactId' =>
//                    [
//                        'leadId',
//                    ],
//                    [
//                        'dealId',
//                    ],
//        ];

        $this->leadsById = [
            'personId' => [
                'leadId',
            ]
        ];

        $this->peopleMultiSelect=[];
        $this->activePeopleMultiSelect = [];
        $this->markedLeadsById = [];
    }
}
