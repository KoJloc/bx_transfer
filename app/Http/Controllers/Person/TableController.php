<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Models\Person;

class TableController extends Controller
{
    public function __invoke()
    {
        $people = Person::all();
        return $people;
    }
}
