<?php

namespace App\Http\Controllers\Entities;

use App\Http\Controllers\Controller;
use App\Http\Traits\AllBXData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request): array
    {
        return AllBXData::get($request);
    }
}
