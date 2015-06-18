<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Checkin;

class TAController extends Controller {

    public function get_console() {
        //Get all of our checkins
        $checkins = Checkin::with("ta")->with("type")->with("user")->orderBy("created_at", "DESC")->get();
        return view("ta.console")->with(["checkins" => $checkins]);
    }

}
