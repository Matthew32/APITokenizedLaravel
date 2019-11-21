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

Route::group([
    'prefix' => 'auth',
], function () {
    //register user
    Route::post('register', 'AuthController@register');
    //make user login
    Route::post('login', 'AuthController@login');
    //make user token refresh
    Route::post('refresh', 'AuthController@refresh');
    //check log in info
    Route::post('me', 'AuthController@me');
});

Route::group(['middleware' => ['jwt']], function () {
    //update user info
    Route::put('user', 'UserController@update');
    //remove user
    Route::delete('user', 'UserController@destroy');
    //create user picture
    Route::get('user/picture', 'UserController@picture');
    //url to check avatar
    Route::get('user/avatar', 'UserController@avatar');
    //get user log in info
    Route::get('user', 'UserController@index');
});



