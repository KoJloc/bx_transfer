<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\CRest;
use App\Models\History;
use App\Models\TransferGroup;
use App\Models\VerifiedUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardController extends Controller
{
    public function dates()
    {
        $groupedDates = History::selectRaw('COUNT(id) AS count, DATE(created_at) AS date')->groupBy('date')->get();

        return response()->json([
            "data" => $groupedDates
        ]);
    }
    public function types()
    {
        $groupedTypes = History::selectRaw('COUNT(id) AS count, entity_type')->groupBy('entity_type')->get();

        return response()->json([
            "data" => $groupedTypes
        ]);
    }

    public function transfer_count()
    {
        $transferedCount = TransferGroup::where('transfer_group_status', 1)->count('id');
        $rollbackCount = TransferGroup::where('rollback_status', 1)->count('id');
        $groupsCount = TransferGroup::count('id');

        $data = [
            0 => [
                'count' => $groupsCount,
                'type' => 'Всего',
            ],
            1 => [
                'count' => round($transferedCount - $rollbackCount),
                'type' => 'Успешно',
            ],
            2 => [
                'count' => $rollbackCount,
                'type' => 'Откачено',
            ],
        ];

        return response()->json([
            "data" => $data,
        ]);
    }
}
