<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\CRest;

class Controller extends BaseController
{
    public $markedPeople;
    public $markedActivePeople;
    public $entitiesByEmployerId = [];



    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->entitiesByEmployerId = [];
        $this->markedPeople = [];
        $this->markedActivePeople = [];
    }
}
