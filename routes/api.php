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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    // return Auth::user();
    return $request->user();
});

// Route::post('/user/register', 'Auth\RegisterController@register');

// Route::post('/user/login', 'Auth\LoginController@login')->name('login');

// Route::get('user/activate/{token}', 'Auth\RegisterController@activate');

// Route::group(['middleware' => 'auth:api'], function(){
//     Route::get('/user/settings', 'SettingsController@index');
// });
