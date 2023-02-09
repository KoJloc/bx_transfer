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
    public $markedPeople;
    public $markedActivePeople;

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

        $this->peopleMultiSelect=[];
        $this->activePeopleMultiSelect = [];
        $this->markedLeadsById = [];
        $this->markedPeople = [];
        $this->markedActivePeople = [];
    }
}
