<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarkedPeopleResource;
use Illuminate\Http\Request;

class MarkedPeopleController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(MarkedPeopleResource $resource)
    {
        $data  = $resource->validated();
//        return $data;
        dd($data);
    }
}
