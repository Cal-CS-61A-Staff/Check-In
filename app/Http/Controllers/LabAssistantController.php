<?php namespace App\Http\Controllers;

class LabAssistantController extends Controller {

    public function get_checkin() {
        return view("la.checkin");
    }
}
