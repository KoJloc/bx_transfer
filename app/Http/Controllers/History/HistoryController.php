<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = request('search');
        $fromDate = '';
        $toDate = '';
        if (!empty(request('date'))) {
            $date = explode(',', request('date'));
            $fromDate = $date[0];
            $toDate = $date[1];
        }
        $limit = (int)request('limit');

        $histories = DB::table('transfer_groups')
            ->when(!empty($fromDate) && !empty($toDate), function($d) use ($fromDate, $toDate) {
                $d->where('created_at', '>=', $fromDate)->where('created_at', '<=', $toDate);
            })
            ->when(!empty($search), function ($q) use ($search) {
                $q->whereId($search);
            })
            ->select('id', 'transfer_group_status', 'rollback_status', 'created_at', 'updated_at')
            ->paginate($limit < 1 ? 10 : $limit);

        return response()->json([
            "data" => $histories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
