<?php

namespace App\Http\Controllers;

use App\Http\Traits\CRest;
use App\Models\VerifiedUser;

class IndexController extends Controller
{
    public function __invoke()
    {
        $validatedUsers = [];
        $check = CRest::setDataE($_REQUEST);
        $user = CRest::call('user.current');
        $rawUsers = VerifiedUser::where('verified', 1)->get();
        foreach ($rawUsers as $rawUser) {
            $validatedUsers[] = $rawUser['bx_id'];
        }
        if(in_array($user['result']['ID'], $validatedUsers)) {
            return view('index');
        } else {
            return response()->view('errors/401', [], 401);
        }
    }
}
