<?php namespace App\Http\Controllers;

use Validator, Auth, Request;
use App\Checkin;
use App\Password;
use App\Audit;
use App\User;
class LabAssistantController extends Controller {

    public function get_checkin() {
        return view("la.checkin");
    }

    public function post_checkin() {
        //Get our inputs
        $input = Request::all();
        //Validate our input
        $validator = Validator::make([
            "location" => $input["location"],
            "date" => $input["date"],
            "time" => $input["time"],
            "gsi" => $input["gsi"],
            "makeup" => $input["makeup"],
            "password" => $input["password"],
        ], [
            "location" => "required|in:0,1,2,3",
            "date" => "required",
            "time" => "required",
            //Todo add requirement for GSI field to match DB gsi list
            "gsi" => "required",
            "makeup" => "required|in:0,1",
            "password" => "required",
        ], [
            "location.required" => "Please go back and choose your section type. (Lab, office hours etc...).",
            "location.in" => "That doesn't appear to be a valid event type (Lab, office hours etc...).",
            "date.required" => "Please choose a date for your check in.",
            "time.required" => "Please go back and choose the start time.",
            "gsi.required" => "Please go back and choose the GSI that is leading your section today",
            "makeup.required" => "Please go back and choose if this is a makeup check in.",
            "makeup.in" => "That doesn't appear to be a valid choice for if this is a makeup check in.",
            "password.required" => "Please have your GSI enter the daily, unique password.",
        ]);
        //Alright validation time
        if ($validator->fails())
        {
            $message = "";
            foreach($validator->errors()->all() as $error)
            {
                $message .= '<div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    ' . $error . '</div>';
            }
            return $message;
        }
        //We now need to validate that the password entered is correct
        $password = Password::where('gsi', '=', $input["gsi"])->first();
        if (empty($password) || $password->password != $input["password"])
        {
            //Add an audit log for this failure
            Audit::log("Failed check in attempt with bad password: " . $input["password"]);
            //Add to our message bag
            $validator->errors()->add("password", "Invalid GSI password. Attempts are logged and monitored.");
            //Return our errors
            $message = "";
            foreach($validator->errors()->all() as $error)
            {
                $message .= '<div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    ' . $error . '</div>';
            }
            return $message;
        }
        //Ok all of our validation is complete and we can now log this checkin
        $checkin = new Checkin;
        $checkin->uid = Auth::user()->id;
        $checkin->location = $input["location"];
        $checkin->date = $input["date"];
        $checkin->time = $input["time"];
        $checkin->gsi = $input["gsi"];
        $checkin->makeup = $input["makeup"];
        //Save our new checkin
        $checkin->save();
        //Make an audit log entry for this checkin
        Audit::log("Check In Successful");
        return 1;
    }

    public function get_attendance() {
        $uid = Auth::user()->id;
        $checkins = Checkin::where("uid", "=", $uid)->with("ta")->with("type")->orderBy("date", "DESC")->get();
        //Create our view
        return view("la.attendance")->with(array("checkins" => $checkins));
    }

    public function get_account() {
        $user = Auth::user();
        return view('la.account')->with(array("user" => $user));
    }
}
