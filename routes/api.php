<?php

use Illuminate\Http\Request;

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

Route::get('/users/{id}/mojifications', 'MojificationController@index');
Route::post('/mojifications', 'MojificationController@store');
Route::post('/users', 'UserController@store');
Route::get('/users/{id}', function ($id) {
    return \App\User::find($id);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
