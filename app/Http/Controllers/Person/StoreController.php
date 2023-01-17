<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Http\Requests\Persone\StoreRequest;
use App\Models\Person;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request)
    {
        $data  = $request->validated();
        $person = Person::create($data);
        return $person;
    }
}
