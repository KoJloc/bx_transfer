<?php

namespace App\Http\Controllers;

use App\Http\Traits\AllBXData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UtilityController extends Controller
{
    public function CacheRefresh(Request $request): array|bool
    {
        Cache::flush();
        Cache::forget('leadStatusList');
        Cache::forget('leadTypeList');
        Cache::forget('regionList');
        Cache::forget('salesDepartmentsLeadList');
        Cache::forget('salesDepartmentsDealList');
        Cache::forget('salesDepartmentsContactList');
        Cache::forget('sourceList');
        Cache::forget('dealTypeList');
        Cache::forget('dealFunnelList');
        Cache::forget('peopleMultiSelect');
        Cache::forget('activePeopleMultiSelect');

        return !Cache::has('leadStatusList')
            ? AllBXData::get($request)
            : array('Кеш не чистится!');
    }
}
