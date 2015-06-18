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

//PUBLIC Routes
    //GET
    Route::get('/', ["as" => "index", "uses" => "IndexController@get_index"]);
    Route::get('login', ["as" => "login", "uses" => "IndexController@get_login", "middleware" => "guest"]);
    Route::get('logout', ["as" => "logout", "uses" => "IndexController@get_logout", "middleware" => "auth"]);
    Route::get('registration', ["as" => "registration", "uses" => "IndexController@get_registration", "middleware" => "guest"]);
    //POST
    Route::post('login', ["as" => "dologin", "uses" => "IndexController@post_login", "middleware" => "guest"]);
    Route::post('registration', ["as" => "doregistration", "uses" => "IndexController@post_registration", "middleware" => "guest"]);

//LA Routes
    //GET
    Route::get('checkin', ["as" => "lacheckin", "uses" => "LabAssistantController@get_checkin", "middleware" => "auth"]);
    Route::get('attendance', ["as" => "laattendance", "uses" => "LabAssistantController@get_attendance", "middleware" => "auth"]);
    Route::get('account', ["as" => "laaccount", "uses" => "LabAssistantController@get_account", "middleware" => "auth"]);
    //POST
    Route::post('docheckin', ["as" => "dolacheckin", "uses" => "LabAssistantController@post_checkin", "middleware" => "auth"]);
    Route::post('account', ["as" => "doaccount", "uses" => "LabAssistantController@post_account", "middleware" => "auth"]);

//TA Routes
    //GET
    Route::get('ta/console', ["as" => "taconsole", "uses" => "TAController@get_console", "middleware" => "auth.ta"]);

