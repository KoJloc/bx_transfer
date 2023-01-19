<?php

use App\Http\Controllers\Person\DeleteController;
use App\Http\Controllers\Person\ShowController;
use App\Http\Controllers\Person\TableController;
use App\Http\Controllers\Person\StoreController;
use App\Http\Controllers\Person\UpdateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Person', 'prefix' => 'people'], function () {
    Route::post('/create', [StoreController::class, '__invoke']);
    Route::post('/', [TableController::class, '__invoke']);
    Route::post('/{person}', [ShowController::class, '__invoke']);
    Route::patch('/{person}', [UpdateController::class,'__invoke']);
    Route::delete('/{person}', [DeleteController::class,'__invoke']);
});
