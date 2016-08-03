<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(
    [
        'middleware' => 'auth',
    ],
    function () {
        Route::get('/', ['as' => 'home', 'uses' => 'HomeController@indexAction']);
        Route::post('changePwd', ['as' => 'change_pwd', 'uses' => 'HomeController@changePwdAction']);
    }
);

Route::group(
    [
        'prefix'    => 'cas',
        'namespace' => 'Cas',
    ],
    function () {
        Route::get('login', ['as' => 'cas_login_page', 'uses' => 'SecurityController@loginPageAction']);
        Route::post('login', ['as' => 'cas_login_action', 'uses' => 'SecurityController@login']);
        Route::get('logout', ['as' => 'cas_logout', 'uses' => 'SecurityController@logout'])->middleware('auth');
        Route::any('validate', ['as' => 'cas_v1validate', 'uses' => 'ValidateController@v1ValidateAction']);
        Route::any('serviceValidate', ['as' => 'cas_v2validate', 'uses' => 'ValidateController@v2ValidateAction']);
        Route::any('p3/serviceValidate', ['as' => 'cas_v3validate', 'uses' => 'ValidateController@v3ValidateAction']);
    }
);

Route::group(
    [
        'namespace'  => 'Admin',
        'middleware' => 'admin',
        'prefix'     => 'admin',
    ],
    function () {
        Route::get('home', ['as' => 'admin_home', 'uses' => 'HomeController@indexAction']);
    }
);