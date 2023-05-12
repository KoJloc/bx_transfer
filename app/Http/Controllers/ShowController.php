<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\History;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transferGroup = request('transfer_group');
        $limit = (int)request('limit');

        $histories = DB::table('histories')
            ->where('transfer_group_ID', '=', $transferGroup)
		->select('id', 'entity_ID', 'entity_type', 'old_responsible_ID', 'new_responsible_ID', 'transfer_status', 'rollback_status', 'created_at', 'updated_at')
		->paginate($limit < 1 ? 10 : $limit);
        
        return response()->json([
            "data" => $histories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	
	
    public function test()
    {
        $search = request('search');
        $limit = (int)request('limit');

        $histories = DB::table('transfer_groups')
		->when(!empty($search), function($q) use($search) {
			$q->whereId($search);
		})
		->select('id', 'transfer_group_status', 'rollback_status', 'created_at', 'updated_at')
		->paginate($limit < 1 ? 10 : $limit);
        
        return response()->json([
            "data" => $histories
        ]);
    }

}
