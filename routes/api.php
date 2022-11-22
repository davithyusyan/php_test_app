<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
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

// AUTH - LOGIN
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    // AUTH - REFRESH TOKEN
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
    // AUTH - LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);

    // USERS
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'list']);
        Route::get('/{id}', [UserController::class, 'getOne']);
        Route::post('/', [UserController::class, 'create']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'delete']);
    });

    // LOCATIONS
    Route::group(['prefix' => 'locations'], function () {
        Route::get('/', [LocationController::class, 'list']);
        Route::get('/{id}', [LocationController::class, 'getOne']);
        Route::post('/', [LocationController::class, 'create']);
        Route::put('/{id}', [LocationController::class, 'update']);
        Route::delete('/{id}', [LocationController::class, 'delete']);
    });
});
