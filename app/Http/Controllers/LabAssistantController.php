<?php namespace App\Http\Controllers;

use Validator, Auth, Request, Hash, Mail, View;
use App\Checkin;
use App\Password;
use App\Audit;
use App\Assignment;
use App\Preference;
use App\User;
use App\Type;
use App\Section;
use App\Announcement;
class LabAssistantController extends Controller {

    public function __construct() {
        $announcements = Announcement::where("hidden", "!=", 0)->orderBy("created_at", "DESC")->get();
        View::share('announcements', $announcements);
    }
    public function get_checkin() {
        //Load our types
        $types = Type::where("hidden", "=", 0)->get();
        $tas = User::where("access", ">", 0)->orderBy("name", "ASC")->get();
        return view("la.checkin")->with(["types" => $types, "tas" => $tas]);
    }

    public function get_assignments() {
        //Get our assignments
        $assignments = Assignment::with("sec.category")->with("sec.ta")->with("sec.ta2")->where("uid", "=", Auth::user()->id)->get();
        //Get our preferences
        $preferences = Preference::with("sec.category")->with("sec.ta")->with("sec.ta2")->where("uid", "=", Auth::user()->id)->get();
        //Get all sections
        $sections = Section::with('category')->with('assigned')->with('ta')->with('ta2')->orderBy('type')->orderBy("mon")->orderBy("tue")->orderBy("wed")->orderBy("thu")->orderBy("fri")->orderBy("sat")->orderBy("sun")->orderBy('start_time')->get();
        $assignmentSids = array();
        foreach ($assignments as $assignment) {
            $assignmentSids[$assignment->section] = $assignment->section;
        }
        //Return our Response
        return view('assignments')->with(["assignmentSids" => $assignmentSids, "sections" => $sections, "assignments" => $assignments, "preferences" => $preferences]);
    }

    public function post_assignments() {
        //Get our input fields
        $hours = Request::input('inputHours');
        $units = Request::input('inputUnits');
        //Ensure we have a value for hours and units
        if ($hours == "" || $units == "") {
            return redirect()->route("laassignments")->with("message", "Hours and Units must be provided.");
        }
        //Ensure both of these numbers are number
        if (!is_numeric($hours) || !is_numeric($units)) {
            return redirect()->route("laassignments")->with("message", "Hours and Units must be numeric.");
        }
        $u = User::findOrFail(Auth::user()->id);
        $u->hours = $hours;
        $u->units = $units;
        $u->save();
        $sections = Request::input('inputSections');
        //Current requested sections
        $preferences = Assignment::where("uid", "=", Auth::user()->id)->get();
        //start by adding all our current preferences
        $preferencesToDelete =  array();
        foreach ($preferences as $preference) {
            $preferencesToDelete[$preference->id] = $preference->id;
        }
        if (!empty($sections)) {
            foreach ($sections as $section) {
                //Ensure that this is a valid section
                $count = Section::where("id", "=", $section)->count();
                if ($count == 0) {
                    return redirect()->route("laassignments")->with("message", "It appears you selected an invalid section.");
                }
                $sData = Section::where("id", "=", $section)->with("assigned")->first();
                if (in_array($section, $preferencesToDelete)) {
                    unset($preferencesToDelete[$section]);
                }
                else if ($sData->max_las != -1 && count($sData->assigned) >= $sData->max_las) {
                    //Too many lab assistants in this section.
                    return redirect()->route("laassignments")->with("message", "It appears one or more of the sections you have requested is now full. Please choose another.");
                }
                else {
                    //All good. Let's make the new assignment
                    $a = new Assignment;
                    $a->uid = Auth::user()->id;
                    $a->section = $section;
                    $a->save();
                }

            }
        }
        foreach ($preferencesToDelete as $ptd) {
            $p = Assignment::findOrFail($ptd);
            $p->delete();
        }
        return redirect()->route("laassignments")->with("message", "Section assignments saved successfully.");
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
        if (Auth::user()->email_notifications == 1)
        {
            Mail::send('emails.checkin', $data, function ($message) use ($data) {
                $message->to($data["email"], $data["name"])->subject('CS61A - Lab Assistant Check In');
            });
        }
        return 1;
    }

    public function get_attendance() {
        $uid = Auth::user()->id;
        $checkins = Checkin::where("uid", "=", $uid)->with("ta")->with("type")->orderBy("created_at", "DESC")->get();
        $total = 0;
        foreach ($checkins as $c) {
            $total += $c->type->hours;
        }
        //Create our view
        return view("la.attendance")->with(array("checkins" => $checkins, "total" => $total));
    }

    public function get_account() {
        $user = Auth::user();
        return view('la.account')->with(array("user" => $user));
    }

    public function post_account() {
        //Get our data
        $input = Request::all();
        if (Request::has('inputEmailNotifications'))
            $input["inputEmailNotifications"] = 1;
        else
            $input["inputEmailNotifications"] = 0;
        //And our user
        $user = Auth::user();
        //This is a bit messy, but it needs to happen. We need two different
        // validators depending on if they are changing their email or not
        if ($input['inputEmail'] != $user->email)
        {
            $validator = Validator::make([
                "name" => $input["inputName"],
                "email" => $input["inputEmail"],
                "password" => $input["inputPassword"],
                "email_notifications" => $input["inputEmailNotifications"],
            ], [
                "name" => "required",
                "email" => "required|email|unique:users,email",
                "password" => "min:8",
                "email_notifications" => "required|in:0,1",
            ], [
                "name.required" => "Please enter your name.",
                "email.required" => "Please enter your email address.",
                "email.email" => "That does not appear to be a valid email address.",
                "email.unique" => "That email appears to be in use with another account.",
                "password.min" => "Please ensure that your password is 8 or more characters long.",
                "email_notifications.required" => "Please specify your email notifications preferences.",
                "email_notifications.in" => "Invalid value for email notifications",
            ]);
        }
        else {
            $validator = Validator::make([
                "name" => $input["inputName"],
                "email" => $input["inputEmail"],
                "password" => $input["inputPassword"],
                "email_notifications" => $input["inputEmailNotifications"],
            ], [
                "name" => "required",
                "email" => "required|email",
                "password" => "min:8",
                "email_notifications" => "required|in:0,1",
            ], [
                "name.required" => "Please enter your name.",
                "email.required" => "Please enter your email address.",
                "email.email" => "That does not appear to be a valid email address.",
                "password.min" => "Please ensure that your password is 8 or more characters long.",
                "email_notifications.required" => "Please specify your email notifications preferences.",
                "email_notifications.in" => "Invalid value for email notifications",
            ]);
        }

        //Grr it is 4:18 AM and cold in here
        if ($validator->fails())
        {
            //Back we go
            return redirect()->route("laaccount")->withInput(Request::except("inputPassword"))->withErrors($validator->errors());
        }
        //Alright validation is complete let's make some changes folks
        $user->name = $input["inputName"];
        $user->email = $input["inputEmail"];
        //Hash up our password.
        if ($input["inputPassword"] != "") {
            $user->password = Hash::make($input["inputPassword"]);
        }
        $user->email_notifications = $input["inputEmailNotifications"];
        //Save our data
        $user->save();
        //Make an audit log entry
        Audit::log("Account information updated");
        //Redirect back to the form and advise them that everything was successful.
        return redirect()->route("laaccount")->with("message", "Your account changes were saved successfully.");
    }

    public function get_queue() {
        return view("la.queue");
    }
}
