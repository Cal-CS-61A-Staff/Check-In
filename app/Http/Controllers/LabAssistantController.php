<?php namespace App\Http\Controllers;

use Validator, Auth, Request, Hash, Mail;
use App\Checkin;
use App\Password;
use App\Audit;
use App\User;
use App\Type;
class LabAssistantController extends Controller {

    public function get_checkin() {
        //Load our types
        $types = Type::all();
        $tas = User::where("access", ">", 0)->orderBy("name", "ASC")->get();
        return view("la.checkin")->with(["types" => $types, "tas" => $tas]);
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
            "location" => "required|exists:types,id",
            "date" => "required",
            "time" => "required",
            "gsi" => "required|exists:passwords,gsi",
            "makeup" => "required|in:0,1",
            "password" => "required",
        ], [
            "location.required" => "Please go back and choose your section type. (Lab, office hours etc...).",
            "location.exists" => "That doesn't appear to be a valid event type (Lab, office hours etc...).",
            "date.required" => "Please choose a date for your check in.",
            "time.required" => "Please go back and choose the start time.",
            "gsi.required" => "Please go back and choose the GSI that is leading your section today",
            "gsi.exists" => "That does not appear to be a valid GSI selection",
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
        //Send an email notification
        $data = ['name' => Auth::user()->name, 'date' => $input["date"], 'time' => $input["time"], 'email' => Auth::user()->email];
        Mail::send('emails.checkin', $data, function($message) use ($data)
        {
            $message->to($data["email"], $data["name"])->subject('CS61A - Lab Assistant Check In');
        });
        return 1;
    }

    public function get_attendance() {
        $uid = Auth::user()->id;
        $checkins = Checkin::where("uid", "=", $uid)->with("ta")->with("type")->orderBy("created_at", "DESC")->get();
        //Create our view
        return view("la.attendance")->with(array("checkins" => $checkins));
    }

    public function get_account() {
        $user = Auth::user();
        return view('la.account')->with(array("user" => $user));
    }

    public function post_account() {
        //Get our data
        $input = Request::all();
        //And our user
        $user = Auth::user();
        //This is a bit messy, but it needs to happen. We need two different
        // validators depending on if they are changing their email or not
        if ($input['inputEmail'] != $user->email)
        {
            $validator = Validator::make([
                "sid" => $input["inputSID"],
                "name" => $input["inputName"],
                "email" => $input["inputEmail"],
                "password" => $input["inputPassword"],
            ], [
                "sid" => "required|integer",
                "name" => "required",
                "email" => "required|email|unique:users,email",
                "password" => "min:8",
            ], [
                "sid.required" => "Please enter your student ID.",
                "sid.integer" => "Your student ID should be numeric.",
                "name.required" => "Please enter your name.",
                "email.required" => "Please enter your email address.",
                "email.email" => "That does not appear to be a valid email address.",
                "email.unique" => "That email appears to be in use with another account.",
                "password.min" => "Please ensure that your password is 8 or more characters long.",
            ]);
        }
        else {
            $validator = Validator::make([
                "sid" => $input["inputSID"],
                "name" => $input["inputName"],
                "email" => $input["inputEmail"],
                "password" => $input["inputPassword"],
            ], [
                "sid" => "required|integer",
                "name" => "required",
                "email" => "required|email",
                "password" => "min:8",
            ], [
                "sid.required" => "Please enter your student ID.",
                "sid.integer" => "Your student ID should be numeric.",
                "name.required" => "Please enter your name.",
                "email.required" => "Please enter your email address.",
                "email.email" => "That does not appear to be a valid email address.",
                "password.min" => "Please ensure that your password is 8 or more characters long.",
            ]);
        }

        //Grr it is 4:18 AM and cold in here
        if ($validator->fails())
        {
            //Back we go
            return redirect()->route("laaccount")->withInput(Request::except("inputPassword"))->withErrors($validator->errors());
        }
        //Alright validation is complete let's make some changes folks
        $user->sid = $input["inputSID"];
        $user->name = $input["inputName"];
        $user->email = $input["inputEmail"];
        if ($input["inputPassword"] != "") {
            $user->password = Hash::make($input["inputPassword"]);
        }
        //Save our data
        $user->save();
        //Make an audit log entry
        Audit::log("Account information updated");
        //Redirect back to the form and advise them that everything was successful.
        return redirect()->route("laaccount")->with("message", "Your account changes were saved successfully.");
    }
}
