<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
Route::post("/user/login", "UserController@login");
Route::group(["middleware" => ["auth:api", "role:admin"]], function () {
    Route::post('/user/register', 'UserController@store');
});

Route::group(["middleware" => ["auth:api"]], function () {

    Route::group(["middleware" => ["role:teacher"]], function () {
        Route::get('/attendance', 'AttendanceController@index');
        Route::post('/attendance', 'AttendanceController@store');
        Route::patch('/attendance', 'AttendanceController@update');
    });

    Route::group(["middleware" => ["role:student"]], function () {
        Route::post("/leave", "LeaveController@store");
        Route::delete("/leave/{leave}", "LeaveController@destroy");
    });

    Route::group(["middleware" => ["role:admin"]], function () {
        Route::get('/admin/leave', 'LeaveController@index');
    });
});

Route::any('{any}', function () {
    throw new HttpException(404, "route not found");
});
