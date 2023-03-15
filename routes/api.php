<?php

use App\Http\Controllers\Entities\EntitiesGetController;
use App\Http\Controllers\Entities\EntitiesUpdateController;
use App\Http\Controllers\Entities\UserController;
use App\Models\User;
use Cassandra\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Entities\ApiUserController;

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

Route::post('/get', [ApiUserController::class, 'get']);
Route::post('/retry', [EntitiesUpdateController::class, 'checkBDforErrors']);

// Защищенные роуты
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['namespace' => 'Entities', 'prefix' => 'entities'], function () {
        Route::post('/', [UserController::class, '__invoke']);
        Route::post('/get', [EntitiesGetController::class, 'vueGet']);
    });
});


