<?php

namespace App\Http\Controllers;

class PersonController extends Controller
{
    public function __invoke(){
        $persons = [
            [
            'id' => 1,
            'name' => 'Jop',
            'age' => 18,
            'job' => 'developer'
            ],
            [
                'id' => 2,
                'name' => 'Bob',
                'age' => 21,
                'job' => 'web developer'
            ],
            [
                'id' => 3,
                'name' => 'John',
                'age' => 24,
                'job' => 'frontend'
            ],
        ];
        return $persons;
    }
}
