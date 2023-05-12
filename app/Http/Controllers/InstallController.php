<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CRest;

class InstallController extends Controller
{
    public function install(Request $request)
    {
        $result = CRest::installApp($request);
        return view('install', compact('result'));
    }
}
