<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Models\Person;

class ShowController extends Controller
{
    public function __invoke(Person $person)
    {
        return $person;
    }
}
