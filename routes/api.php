<?php

use App\Http\Controllers\Entities\ApiUserController;
use App\Http\Controllers\Entities\UserController;
use App\Http\Controllers\Entities\EntitiesGetController;
use App\Http\Controllers\Entities\EntitiesUpdateController;
use App\Http\Controllers\Entities\EntitiesRollbackController;
use App\Http\Controllers\History\HistoryController;
use App\Http\Controllers\History\ShowController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VerifiedUserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
*/


Route::middleware('auth:sanctum')->post('/user', function (Request $request) {
    return $request->user();
});

//Route::post('/sanctum/token', function (Request $request) {
//    $request->validate([
//        'email' => 'required|email',
//        'password' => 'required',
//        'secret_key' => 'required',
//    ]);
//    $user = User::where('email', $request->email)->first();
//
//    if (! $user || ! Hash::check($request->password, $user->password)) {
//        throw ValidationException::withMessages([
//            'email' => ['The provided credentials are incorrect.'],
//        ]);
//    }
//    return $user->createToken($request->secret_key)->plainTextToken;
//});

Route::post('/index', [IndexController::class, '__invoke']);
Route::post('/transfer/install', [InstallController::class, 'install']);
Route::post('/cache/refresh', [UtilityController::class, 'CacheRefresh']);

// API с приложения Никитоса
Route::post('/transfer/user', [ApiUserController::class, 'get']);
Route::post('/transfer/rollback', [EntitiesRollbackController::class, 'get']);
Route::post('/transfer/message/test', [EntitiesUpdateController::class, 'generateTransferMessageTest']);
Route::post('/transfer/error/retry', [EntitiesUpdateController::class, 'checkBDforErrors']);

// API для внутренней работы с VUE
Route::group(['namespace' => 'Entities', 'prefix' => 'entities'], function () {
    Route::post('/', [UserController::class, 'index']);
    Route::post('/params/set', [EntitiesGetController::class, 'vueRequestProcess']);
});

Route::post('/histories/history', [HistoryController::class, 'index']);
Route::post('/histories/history/show', [ShowController::class, 'index']);

Route::post('/users/get', [VerifiedUserController::class, 'index']);
Route::post('/users/store', [VerifiedUserController::class, 'store']);
Route::post('/users/verify', [VerifiedUserController::class, 'update']);

Route::post('/dashboard/dates', [DashboardController::class, 'dates']);
Route::post('/dashboard/types', [DashboardController::class, 'types']);
Route::post('/dashboard/counts', [DashboardController::class, 'transfer_count']);

Route::post('/emp_string_test', [EntitiesUpdateController::class, 'generateEmployersStringToMessagesTest']);






