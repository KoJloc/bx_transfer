<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\CRest;
use App\Models\History;
use App\Models\VerifiedUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VerifiedUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = request('search');
        $limit = (int)request('limit');

        $histories = DB::table('verified_users')
            ->when(!empty($search), function ($q) use ($search) {
                    $q->where('full_name', 'like', '%' . $search . '%');
            })
            ->select('id', 'full_name', 'image', 'job', 'active', 'verified', 'created_at', 'updated_at')
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
    public function store()
    {
        $rawUsers = CRest::firstBatch('user.get');

        foreach ($rawUsers as $user) {
            $selectedUser = [
                'bx_id' => $user['ID'],
                'full_name' => trim($user['LAST_NAME'] . ' ' . $user['NAME'] . ' ' . $user['SECOND_NAME']),
                'image' => $user['PERSONAL_PHOTO'],
                'job' => $user['WORK_POSITION'],
                'active' => $user['ACTIVE'],
            ];
                VerifiedUser::updateOrCreate(['bx_id' => $user['ID']], $selectedUser);
        }
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
    public function update(Request $request)
    {
        return VerifiedUser::whereId($request->id)->update(['verified' => $request->verified]);
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
