<?php

namespace App\Http\Controllers\Entities;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRest;
use App\Jobs\ProceedGetEntities;
use App\Jobs\ProceedGetFiltredEntities;
use App\Models\ParamsFromRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EntitiesGetController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function vueRequestProcess(Request $request) {
        $params = [];

        $data = $request->all();

        info($data);

        if(empty($data['departments']) || empty($data['onlyActiveDepartments'])) {
            return response()->json([
                'data' => 'error'
            ]);
        }

        foreach ($data['departments'] as $employee) {
            $fromUsers[] = $employee['id'];
        }
        foreach ($data['onlyActiveDepartments'] as $employee) {
            $toUsers[] = $employee['id'];
        }
        $response = ParamsFromRequest::create([
            'from_id' => json_encode($fromUsers),
            'to_id' => json_encode($toUsers),
        ]);
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $params[$key] = $value;
            }
        }
        ProceedGetFiltredEntities::dispatch($response, $params, $fromUsers, $toUsers);
        
        return response()->json([
            'data' => \Queue::size()
        ]);
    }

    public function set($request, $fromUsers, $toUsers)
    {
        if (isset($fromUsers) && !empty($fromUsers['from'])) {
            if (isset($toUsers) && !empty($toUsers['to'])) {
                if (isset($request['transferEntities']['head']) && $request['transferEntities']['head']) {
                    $is_head = 1;
                    $response = ParamsFromRequest::create([
                        'from_id' => json_encode($fromUsers['from']),
                        'to_id' => json_encode($toUsers['to']),
                        'is_head' => $is_head,
                    ]);
                } else {
                    $response = ParamsFromRequest::create([
                        'from_id' => json_encode($fromUsers['from']),
                        'to_id' => json_encode($toUsers['to']),
                    ]);
                }
            } else {
                return 'Ошибка: не на кого передавать';
            }
        } else {
            return 'Ошибка: не с кого передавать';
        }
        return ProceedGetEntities::dispatch($response, $fromUsers, $toUsers);
    }
}
