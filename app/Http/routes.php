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
        return redirect("https://docs.google.com/a/berkeley.edu/presentation/d/1CJMDtzof49vVB4OXJ7pybmnLbOsWomPBpF4yzVdfPNE");
    });

    //GET
    Route::get('/', ["as" => "index", "uses" => "IndexController@get_index"]);
    Route::get('login', ["as" => "login", "uses" => "IndexController@get_login", "middleware" => "guest"]);
    Route::get('logout', ["as" => "logout", "uses" => "IndexController@get_logout", "middleware" => "auth"]);
    Route::get('reset', ["as" => "reset", "uses" => "IndexController@get_reset", "middleware" => "guest"]);
    Route::get('registration', ["as" => "registration", "uses" => "IndexController@get_login", "middleware" => "guest"]);
    Route::get('information', ["as" => "information", "uses" => "IndexController@get_information"]);
    Route::get('tokenauth/{token}', ["as" => "tokenauth", "uses" => "IndexController@get_tokenauth", "middleware" => "guest"]);
    //POST
    Route::post('login', ["as" => "dologin", "uses" => "IndexController@post_login", "middleware" => "guest"]);
    Route::post('registration', ["as" => "doregistration", "uses" => "IndexController@post_registration", "middleware" => "guest"]);
    Route::post('reset', ["as" => "doreset", "uses" => "IndexController@post_reset", "middleware" => "guest"]);

//LA Routes
    //GET
    Route::get('account', ["as" => "laaccount", "uses" => "LabAssistantController@get_account", "middleware" => "auth"]);
    Route::get('assignments', ["as" => "laassignments", "uses" => "LabAssistantController@get_assignments", "middleware" => "auth"]);
    Route::get('attendance', ["as" => "laattendance", "uses" => "LabAssistantController@get_attendance", "middleware" => "auth"]);
    Route::get('checkin', ["as" => "lacheckin", "uses" => "LabAssistantController@get_checkin", "middleware" => "auth"]);
    Route::get('queue', ["as" => "laqueue", "uses" => "LabAssistantController@get_queue", "middleware" => "auth"]);
    Route::get('solutions', ["as" => "lasolutions", "uses" => "LabAssistantController@get_solutions", "middleware" => "auth"]);
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
    Route::get("ta/checkin/remove/{id}", ["as" => "tacheckinremove", "uses" => "TAController@post_checkin_remove", "middleware" => "auth.tutor"]);
    //POST
    Route::post("ta/update/password", ["as" => "taupdatepassword", "uses" => "TAController@post_update_password", "middleware" => "auth.tutor"]);
    Route::post("ta/user/checkin", ["as" => "tacheckinuser", "uses" => "TAController@post_checkin_user", "middleware" => "auth.tutor"]);
    Route::post("ta/user/checkin/edit", ["as" => "taeditcheckinuser", "uses" => "TAController@post_edit_checkin_user", "middleware" => "auth.tutor"]);
    Route::post("ta/type", ["as" => "taupdatetype", "uses" => "TAController@post_update_type", "middleware" => "auth.ta"]);
    Route::post("ta/new", ["as" => "tanewtype", "uses" => "TAController@post_new_type", "middleware" => "auth.ta"]);
    Route::post("ta/announcement/new", ["as" => "taannouncementnew", "uses" => "TAController@post_announcement_new", "middleware" => "auth.tutor"]);
    Route::post("ta/section/new", ["as" => "tasectionnew", "uses" => "TAController@post_section_new", "middleware" => "auth.ta"]);
    Route::post("ta/section/import", ["as" => "tasectionimport", "uses" => "TAController@post_section_import", "middleware" => "auth.ta"]);
    Route::post("ta/section/edit", ["as" => "tasectionedit", "uses" => "TAController@post_section_edit", "middleware" => "auth.ta"]);
    Route::post("ta/section/assign", ["as" => "tasectionassign", "uses" => "TAController@post_section_assign", "middleware" => "auth.ta"]);
    Route::post("ta/feedback/add", ["as" => "tafeedbackadd", "uses" => "TAController@post_feedback_add", "middleware" => "auth.tutor"]);
//    Route::post("ta/section/unassign", ["as" => "tasectionunassign", "uses" => "TAController@post_section_unassign", "middleware" => "auth.ta"]);
    Route::post("ta/section/addla", ["as" => "tasectionaddla", "uses" => "TAController@post_section_addla", "middleware" => "auth.ta"]);
    Route::post("ta/section/unassign", ["as" => "tasectionunassign", "uses" => "TAController@post_section_unassign", "middleware" => "auth.ta"]);
    Route::post("ta/settings/save", ["as" => "tasavesettings", "uses" => "TAController@post_settings_save", "middleware" => "auth.ta"]);

// OAuth Routes
    Route::get("auth/ok", ["as" => "oauth", "uses" => "IndexController@get_oauth", "middleware" => "guest"]);

// Admin/Maintenance Routes
    //GET
    Route::get('admin/import_checkins', ["as" => "adminimportcheckins", "uses" => "AdminController@get_import_checkins", "middleware" => "auth.ta"]);

// Development Routes
    //GET
    Route::get('dev/login/{id}', ["as" => "devlogin", "uses" => "DevController@get_login", "middleware" => "dev"]);

