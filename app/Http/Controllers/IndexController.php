<?php

namespace App\Http\Controllers;

use App\Http\Traits\CRest;

class IndexController extends Controller
{
    public function __invoke()
    {
//        CRest::setDataE($_REQUEST);
//        info($_REQUEST);
//        $user = CRest::call('user.current');
//        info($user);
        return view('index');
    }
}
