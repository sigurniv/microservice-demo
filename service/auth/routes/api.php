<?php

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

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('token', '\App\Api\Auth\Controllers\AuthController@getToken');
        Route::get('user', '\App\Api\Auth\Controllers\AuthController@getUser');
        Route::get('token/refresh', '\App\Api\Auth\Controllers\AuthController@refreshToken');
    });
});
