<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Http\Requests\Persone\StoreRequest;
use App\Models\Person;

class IndexController extends Controller
{
    public function __invoke()
    {
        return Person::all();
    }
}
