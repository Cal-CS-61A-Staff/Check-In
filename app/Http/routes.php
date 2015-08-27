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

    //Redirect routes
    Route::get('meeting/slides', function() {
        return redirect("https://docs.google.com/presentation/d/1NbctPvdCWsd_YVAxDyvG2whwcItVdWcAjrS8s4KszQo/");
    });

    //GET
    Route::get('/', ["as" => "index", "uses" => "IndexController@get_index"]);
    Route::get('login', ["as" => "login", "uses" => "IndexController@get_login", "middleware" => "guest"]);
    Route::get('logout', ["as" => "logout", "uses" => "IndexController@get_logout", "middleware" => "auth"]);
    Route::get('reset', ["as" => "reset", "uses" => "IndexController@get_reset", "middleware" => "guest"]);
    Route::get('registration', ["as" => "registration", "uses" => "IndexController@get_registration", "middleware" => "guest"]);
    Route::get('information', ["as" => "information", "uses" => "IndexController@get_information"]);
    Route::get('tokenauth/{token}', ["as" => "tokenauth", "uses" => "IndexController@get_tokenauth", "middleware" => "guest"]);
    //POST
    Route::post('login', ["as" => "dologin", "uses" => "IndexController@post_login", "middleware" => "guest"]);
    Route::post('registration', ["as" => "doregistration", "uses" => "IndexController@post_registration", "middleware" => "guest"]);
    Route::post('reset', ["as" => "doreset", "uses" => "IndexController@post_reset", "middleware" => "guest"]);

//LA Routes
    //GET
    Route::get('checkin', ["as" => "lacheckin", "uses" => "LabAssistantController@get_checkin", "middleware" => "auth"]);
    Route::get('attendance', ["as" => "laattendance", "uses" => "LabAssistantController@get_attendance", "middleware" => "auth"]);
    Route::get('assignments', ["as" => "laassignments", "uses" => "LabAssistantController@get_assignments", "middleware" => "auth"]);
    Route::get('account', ["as" => "laaccount", "uses" => "LabAssistantController@get_account", "middleware" => "auth"]);
    //POST
    Route::post('docheckin', ["as" => "dolacheckin", "uses" => "LabAssistantController@post_checkin", "middleware" => "auth"]);
    Route::post('account', ["as" => "doaccount", "uses" => "LabAssistantController@post_account", "middleware" => "auth"]);
    Route::post('assignments', ["as" => "doassignments", "uses" => "LabAssistantController@post_assignments", "middleware" => "auth"]);

//TA/Tutor Routes
    //GET
    Route::get('ta/console', ["as" => "taconsole", "uses" => "TAController@get_console", "middleware" => "auth.tutor"]);
    Route::get('ta/download/checkins', ["as" => "tadownloadcheckins", "uses" => "TAController@get_download_checkins", "middleware" => "auth.tutor"]);
    Route::get('ta/download/roster', ["as" => "tadownloadroster", "uses" => "TAController@get_download_roster", "middleware" => "auth.tutor"]);
    Route::get('ta/user/promote/{id}', ["as" => "tauserpromote", "uses" => "TAController@get_user_promote", "middleware" => "auth.ta"]);
    Route::get('ta/user/promote/tutor/{id}', ["as" => "tauserpromotetutor", "uses" => "TAController@get_user_promote_tutor", "middleware" => "auth.ta"]);
    Route::get('ta/user/demote/{id}', ["as" => "tauserdemote", "uses" => "TAController@get_user_demote", "middleware" => "auth.ta"]);
    Route::get("ta/announcement/delete/{id}", ["as" => "taannouncementdelete", "uses" => "TAController@get_announcement_delete", "middleware" => "auth.tutor"]);
    Route::get("ta/announcement/visibility/{id}", ["as" => "taannouncementvisibility", "uses" => "TAController@get_announcement_visibility", "middleware" => "auth.tutor"]);
    Route::get("ta/section/delete/{sid}", ["as" => "tasectiondelete", "uses" => "TAController@get_section_delete", "middleware" => "auth.ta"]);
    //POST
    Route::post("ta/update/password", ["as" => "taupdatepassword", "uses" => "TAController@post_update_password", "middleware" => "auth.tutor"]);
    Route::post("ta/user/checkin", ["as" => "tacheckinuser", "uses" => "TAController@post_checkin_user", "middleware" => "auth.tutor"]);
    Route::post("ta/type", ["as" => "taupdatetype", "uses" => "TAController@post_update_type", "middleware" => "auth.ta"]);
    Route::post("ta/new", ["as" => "tanewtype", "uses" => "TAController@post_new_type", "middleware" => "auth.ta"]);
    Route::post("ta/announcement/new", ["as" => "taannouncementnew", "uses" => "TAController@post_announcement_new", "middleware" => "auth.tutor"]);
    Route::post("ta/section/new", ["as" => "tasectionnew", "uses" => "TAController@post_section_new", "middleware" => "auth.ta"]);
    Route::post("ta/section/edit", ["as" => "tasectionedit", "uses" => "TAController@post_section_edit", "middleware" => "auth.ta"]);

