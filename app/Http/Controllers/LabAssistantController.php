<?php namespace App\Http\Controllers;

class LabAssistantController extends Controller {

    public function __construct() {
        //Register our auth middleware
        $this->middleware("auth");
    }

    public function get_checkin() {
        return view("la.checkin");
    }
}
