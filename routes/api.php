<?php

use App\Http\Controllers\Person\LeadController;
use App\Http\Controllers\Person\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Защищенные роуты
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['namespace' => 'Person', 'prefix' => 'people'], function () {
        Route::post('/lead', [LeadController::class, '__invoke']);
        Route::post('/', [UserController::class, '__invoke']);
    });
});
