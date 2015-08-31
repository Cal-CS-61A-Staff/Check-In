<?php namespace App\Http\Controllers;

use App\Announcement;
use Response, Auth, Request, Validator, View;
use Illuminate\Routing\Controller;
use App\Checkin;
use App\User;
use App\Audit;
use App\Type;
use App\Password;
use App\Section;
use App\Assignment;

class TAController extends Controller {

    public function __construct() {
        $announcements = Announcement::where("hidden", "!=", 0)->orderBy("created_at", "DESC")->get();
        View::share('announcements', $announcements);
    }
    public function get_console() {
        //Get all of our checkins
        $checkins = Checkin::with("ta")->with("type")->with("user")->orderBy("created_at", "ASC")->get();
        //Get our hours per user
        $user_hours = Checkin::userHours($checkins);
        $checkins_per_week = Checkin::perWeek($checkins);
        $checkins_unique_per_week = Checkin::uniquePerWeek($checkins);
        $checkins_per_staff = Checkin::perStaff($checkins);
        //Get all of our users
        $users = User::with("assignments.sec.category")->orderBy("name", "ASC")->get();
        //Get our assigned hours
        $assigned_hours = User::get_assignedHours($users);
        //Get under hours
        $under_hours = User::get_underHours($users, $assigned_hours);
        //Get over hours
        $over_hours = User::get_overHours($users, $assigned_hours);
        //Get assignments
        $assignments = Assignment::with("sec")->with("user")->get();
        //Get double booked
        $double_booked = User::get_doubleBooked($assignments);
        //Get our password
        $password = Password::where("gsi", "=", Auth::user()->id)->first()->password;
        //Get our gsis
        $gsis = User::where('access', '>', 0)->orderBy("name", "ASC")->get();
        //Get our types
        $types = Type::all();
        //Get our audits
        $audits = Audit::with("user")->orderBy('created_at', 'DESC')->get();
        //Get our sections
        $sections = Section::with("pref.user")->with("assigned.user")->with("ta")->with("ta2")->with("category")->orderBy("type", "ASC")->get();
        //Get our announcements
        $announcements = Announcement::with("user")->orderBy("hidden", "DESC")->orderBy("created_at", "DESC")->get();
        return view("ta.console")->with(["double_booked" => $double_booked, "over_hours" => $over_hours, "under_hours" => $under_hours, "assigned_hours" => $assigned_hours,"sections" => $sections, "user_hours" => $user_hours, "checkins_unique_per_week" => $checkins_unique_per_week, "checkins_per_staff" => $checkins_per_staff,"checkins_per_week" => $checkins_per_week, "audits" => $audits, "announcements_ta" => $announcements, "gsis" => $gsis, "types" => $types, "checkins" => $checkins, "users" => $users, "password" => $password]);
    }

    public function post_update_password() {
        $password = Request::input("inputPassword");
        if ($password == "") {
            //Ok GSIs you can't have an empty password...
            return redirect()->route("taconsole")->with("message", "ERROR: You cannot have an empty secret word.");
        }
        //Update the password
        $p = Password::where('gsi', '=', Auth::user()->id)->first();
        $p->password = $password;
        $p->save();
        return redirect()->route("taconsole")->with("message", "You have successfully updated your secret word");
    }

    public function post_checkin_user() {
        $input = Request::all();
        //Start some validation
        $validator = Validator::make([
            "location" => $input["inputLocation"],
            "date" => $input["inputDate"],
            "time" => $input["inputTime"],
            "gsi" => $input["inputGSI"],
            "makeup" => $input["inputMakeup"],
        ], [
            "location" => "required|exists:types,id",
            "date" => "required",
            "time" => "required",
            "gsi" => "required|exists:passwords,gsi",
            "makeup" => "required|in:0,1",
        ], [
            "location.required" => "Please go back and choose your section type. (Lab, office hours etc...).",
            "location.exists" => "That doesn't appear to be a valid event type (Lab, office hours etc...).",
            "date.required" => "Please choose a date for your check in.",
            "time.required" => "Please go back and choose the start time.",
            "gsi.required" => "Please go back and choose the GSI that is leading your section today",
            "gsi.exists" => "That does not appear to be a valid GSI selection",
            "makeup.required" => "Please go back and choose if this is a makeup check in.",
            "makeup.in" => "That doesn't appear to be a valid choice for if this is a makeup check in.",
        ]);
        //Run our validator
        if ($validator->fails())
        {
            return redirect()->route('taconsole')->with("message", "Your form submission had errors. Please ensure you have filled out all of the fields.");
        }
        //Create the new checkin for the user
        $user = User::findOrFail($input["inputUID"]);
        //Ok all of our validation is complete and we can now log this checkin
        $checkin = new Checkin;
        $checkin->uid = $input["inputUID"];
        $checkin->location = $input["inputLocation"];
        $checkin->date = $input["inputDate"];
        $checkin->time = $input["inputTime"];
        $checkin->gsi = $input["inputGSI"];
        $checkin->makeup = $input["inputMakeup"];
        //Save our new checkin
        $checkin->save();
        //Make an audit log entry for this checkin
        Audit::log("GSI " . Auth::user()->name . " manually checked in " . $user->name);
        return redirect()->route("taconsole")->with("message", "Manual check in for " . $user->name . " was successful.");
    }

    public function get_user_promote_tutor($id) {
        $user = User::findOrFail($id);
        if ($user->is_gsi() || $user->is_tutor())
            return redirect()->route("taconsole")->with("message", "This user is already a TA or Tutor");
        //Promote the user to tutor
        $user->access = 0.5;
        $user->save();
        //Create an audit log for this
        Audit::log("Promoted from Lab Assistant to Tutor by " . Auth::user()->name);
        //Set a default secret word for them
        $password = new Password;
        $password->gsi = $id;
        $password->password = "recursion";
        $password->save();
        //Let them know all is ok
        return redirect()->route("taconsole")->with("message", $user->name . " successfully promoted from Lab Assistant to Tutor.");
    }

    public function get_user_promote($id) {
        $user = User::findOrFail($id);
        if ($user->is_gsi())
            return redirect()->route("taconsole")->with("message", "This user is already a TA.");
        //Ok promote the user
        $user->access = 1;
        $user->save();
        //Create an audit log for this
        Audit::log("Promoted from Lab Assistant to GSI by " . Auth::user()->name);
        //Set a default secret word for them
        $password = new Password;
        $password->gsi = $id;
        $password->password = "recursion";
        $password->save();
        //Let them know all is ok
        return redirect()->route("taconsole")->with("message", $user->name . " successfully promoted to GSI.");
    }

    public function get_user_demote($id) {
        $user = User::findOrFail($id);
        if ($user->access == 0)
            return redirect()->route("taconsole")->with("message", "This user is not currently a TA or Tutor and has only lab assistant permissions.");
        //Ok promote the user
        $user->access = 0;
        $user->save();
        //Create an audit log for this
        Audit::log("Demoted to Lab Assistant by " . Auth::user()->name);
        //Delete their password
        $password = Password::where("gsi", "=", $user->id)->first();
        $password->delete();
        //Let them know all is ok
        return redirect()->route("taconsole")->with("message", $user->name . " successfully demoted to Lab Assistant.");
    }

    public function post_update_type() {
        //Get our TID
        $tid = Request::input("inputTID");
        //Get our Type name
        $name = Request::input("inputName");
        //Get our hours
        $hours = Request::input("inputHours");
        //Hidden
        $hidden = Request::input("inputHidden");
        if ($hidden !=  1)
            $hidden = 0;
        //Ensure that our TID is valid
        $type = Type::findOrFail($tid);
        //Ensure that the member set a name for the new event type and that it does not match another type
        $otherType = Type::where("name", "=", $name)->count();
        if ($otherType > 1 || $name == "")
            return redirect()->route("taconsole")->with("message", "Either your type was empty or you entered an existing type.");
        if (!is_numeric($hours)) {
            return redirect()->route("taconsole")->with("message", "Ensure you are entering a numeric value for hours in your event type.");
        }
        //Alright all good let's update the model
        $type->name = $name;
        $type->hours = $hours;
        $type->hidden = $hidden;
        //And finally the DB
        $type->save();
        //Let the user know things are well :)
        return redirect()->route("taconsole")->with("message", "The " . $name . " type was updated successfully");
    }


    public function post_new_type() {
        //Get our name
        $name = Request::input("inputName");
        $hours = Request::input("inputHours");
        $hidden = Request::input("inputHidden");
        if ($hidden != 1)
            $hidden = 0;
        $otherTypes = Type::where("name", "=", $name)->count();
        if ($otherTypes > 0 ||$name == "")
            return redirect()->route("taconsole")->with("message", "Either your new event type is empty or an existing event type exists with the same name.");
        if (!is_numeric($hours)) {
            return redirect()->route("taconsole")->with("message", "Ensure you are entering a numeric value for hours in your new event type.");
        }
        //Create our model
        $type = new Type;
        $type->name = $name;
        $type->hours = $hours;
        $type->hidden = $hidden;
        //Push the model to the DB
        $type->save();
        //Alert the user
        return redirect()->route("taconsole")->with("message", "Your new event type was created successfully.");
    }

    public function get_download_checkins() {
        $checkins = Checkin::with("ta")->with("type")->with("user")->orderBy("created_at", "DESC")->get();
        $file = storage_path() . "/app/checkins.csv";
        $handle = fopen($file, 'w+');
        fputcsv($handle, array('Name', 'Type', 'Hours', 'Date', 'Start Time', 'GSI', 'Makeup', 'Logged At'));
        foreach($checkins as $checkin) {
            if ($checkin->makeup == 1)
                $makeup = "Yes";
            else
                $makeup = "No";
            fputcsv($handle, array($checkin->user->name, $checkin->type->name, $checkin->type->hours, $checkin->date, $checkin->time, $checkin->ta->name, $makeup, $checkin->created_at));
        }
        fclose($handle);
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        return Response::download($file, 'checkins.csv', $headers);
    }

    public function get_download_roster() {
        $users = User::with("checkins")->orderBy("name", "ASC")->get();
        $checkins = Checkin::all();
        $user_hours = Checkin::userHours($checkins);
        $file = storage_path() . "/app/roster.csv";
        $handle = fopen($file, 'w+');
        fputcsv($handle, array('Name', 'Email', 'GSI', 'Total # of Hours', 'Total # of Check Ins', 'Created At'));
        foreach($users as $user) {
            if ($user->access > 0)
                $gsi = "YES";
            else
                $gsi = "No";
            fputcsv($handle, array($user->name, $user->email, $gsi, $user_hours[$user->id], count($user->checkins), $user->created_at));
        }
        fclose($handle);
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        return Response::download($file, 'roster.csv', $headers);
    }

    public function post_announcement_new() {
        //Get our input
        $header = Request::input('inputHeader');
        $body = Request::input('inputBody');
        //Are these filled in?
        if ($header == "" || $body == "")
            return redirect()->route('taconsole')->with("message", "Please ensure that you enter both a header and body for your announcement");
        //All clear, let's create a new announcement
        $announcement = new Announcement;
        $announcement->header = $header;
        $announcement->body = $body;
        $announcement->author = Auth::user()->id;
        //Announcement is not hidden by default
        $announcement->hidden = 0;
        $announcement->save();
        //Redirect back to the ta console
        return redirect()->route('taconsole')->with("message", "Your announcement was created and is now public.");
    }

    public function get_announcement_visibility($id) {
        $announcement = Announcement::findOrFail($id);
        if ($announcement->hidden == 0)
            $announcement->hidden = 1;
        else
            $announcement->hidden = 0;
        //Save our announcement
        $announcement->save();
        return redirect()->route('taconsole')->with("message", "The announcement visibility was updated successfully");
    }

    public function get_announcement_delete($id) {
        $announcement = Announcement::findOrFail($id);
        //Delete it
        $announcement->delete();
        return redirect()->route('taconsole')->with("message", "The announcement was deleted successfully");
    }

    public function post_section_new() {
        //Get all of our data
        $type = Request::input('inputType');
        $location = Request::input('inputLocation');
        $gsi = Request::input('inputGSI');
        $second_gsi = Request::input('inputSecond_GSI');
        $maxLas = Request::input('inputMaxLas');
        $mon = Request::input('inputMon');
        $tue = Request::input('inputTue');
        $wed = Request::input('inputWed');
        $thu = Request::input('inputThu');
        $fri = Request::input('inputFri');
        $sat = Request::input('inputSat');
        $sun = Request::input('inputSun');
        $start_time = Request::input('inputStartTime');
        $end_time = Request::input('inputEndTime');
        if ($second_gsi != -1) {
            $validator = Validator::make([
                "type" => $type,
                "location" => $location,
                "gsi" => $gsi,
                "second_gsi" => $second_gsi,
                "maxLas" => $maxLas,
                "mon" => $mon,
                "tue" => $tue,
                "wed" => $wed,
                "thu" => $thu,
                "fri" => $fri,
                "sat" => $sat,
                "sun" => $sun,
                "start_time" => $start_time,
                "end_time" => $end_time,
            ], [
                "type" => "required|exists:types,id",
                "gsi" => "required|exists:passwords,gsi",
                "second_gsi" => "required_with:gsi|different:gsi|exists:passwords,gsi",
                "maxLas" => "required|integer|min:-1",
                "location" => "required",
                "mon" => "in:,0,1",
                "tue" => "in:,0,1",
                "wed" => "in:,0,1",
                "thu" => "in:,0,1",
                "fri" => "in:,0,1",
                "sat" => "in:,0,1",
                "sun" => "in:,0,1",
                "start_time" => "required",
                "end_time" => "required",
            ], [
                "type.required" => "Please select a section type.",
                "type.exists" => "That does not appear to be a valid section type.",
                "gsi.required" => "Please select a GSI.",
                "gsi.exists" => "That does not appear to be a valid GSI.",
                "second_gsi.exists" => "That does not appear to be a valid GSI.",
                "second_gsi.different" => "You may not choose the same GSI as the second GSI. Simply leave the second GSI field empty.",
                "maxLas.required" => "Please enter the maximum amount of lab assistants for this section.",
                "maxLas.integer" => "The max amount of lab assistants needs to be an integer value.",
                "maxLas.min" => "Please enter a max lab assistants integer value equal to or greater than -1",
                "location.required" => "Please enter a location for the section."
            ]);
        }
        else {
            $validator = Validator::make([
                "type" => $type,
                "location" => $location,
                "gsi" => $gsi,
                "second_gsi" => $second_gsi,
                "maxLas" => $maxLas,
                "mon" => $mon,
                "tue" => $tue,
                "wed" => $wed,
                "thu" => $thu,
                "fri" => $fri,
                "sat" => $sat,
                "sun" => $sun,
                "start_time" => $start_time,
                "end_time" => $end_time,
            ], [
                "type" => "required|exists:types,id",
                "gsi" => "required|exists:passwords,gsi",
                "maxLas" => "required|integer|min:-1",
                "location" => "required",
                "mon" => "in:,0,1",
                "tue" => "in:,0,1",
                "wed" => "in:,0,1",
                "thu" => "in:,0,1",
                "fri" => "in:,0,1",
                "sat" => "in:,0,1",
                "sun" => "in:,0,1",
                "start_time" => "required",
                "end_time" => "required",
            ], [
                "type.required" => "Please select a section type.",
                "type.exists" => "That does not appear to be a valid section type.",
                "gsi.required" => "Please select a GSI.",
                "gsi.exists" => "That does not appear to be a valid GSI.",
                "second_gsi.exists" => "That does not appear to be a valid GSI.",
                "second_gsi.different" => "You may not choose the same GSI as the second GSI. Simply leave the second GSI field empty.",
                "maxLas.required" => "Please enter the maximum amount of lab assistants for this section.",
                "maxLas.integer" => "The max amount of lab assistants needs to be an integer value.",
                "maxLas.min" => "Please enter a max lab assistants integer value equal to or greater than -1",
                "location.required" => "Please enter a location for the section."
            ]);
        }
        //Route our validator
        if ($validator->fails()) {
           return redirect()->route('taconsole')->withErrors($validator);
        }
        //Create a new instance of our Section model
        $section = new Section;
        $section->type = $type;
        $section->location = $location;
        $section->gsi = $gsi;
        $section->second_gsi = $second_gsi;
        $section->max_las = $maxLas;
        //Yes someone who reads this and thinks it looks as
        //awful as I do. Please make a PR and fix this before
        //I get too mad looking at it and come up with a more
        //elegant solution using a loop. :P
        if ($mon != 1)
            $section->mon = 0;
        else
            $section->mon = 1;
        if ($tue != 1)
            $section->tue = 0;
        else
            $section->tue = 1;
        if ($wed != 1)
            $section->wed = 0;
        else
            $section->wed = 1;
        if ($thu != 1)
            $section->thu = 0;
        else
            $section->thu = 1;
        if ($fri != 1)
            $section->fri = 0;
        else
            $section->fri = 1;
        if ($sat != 1)
            $section->sat = 0;
        else
            $section->sat = 1;
        if ($sun != 1)
            $section->sun = 0;
        else
            $section->sun = 1;
        $section->start_time = $start_time;
        $section->end_time = $end_time;
        //Push to the DB
        $section->save();
        //Return a redirect Response
        return redirect()->route("taconsole")->with("message", "The new section was successfully created.");
    }

    public function get_section_delete($sid) {
        $section = Section::findOrFail($sid);
        //TODO remove lab assistant assignments for this section
        //Delete the section
        $section->delete();
        return redirect()->route("taconsole")->with("message", "The section was successfully deleted.");
    }
    public function post_section_edit() {
        //Get all of our data
        $sid = Request::input('inputSID');
        $type = Request::input('inputType');
        $location = Request::input('inputLocation');
        $gsi = Request::input('inputGSI');
        $second_gsi = Request::input('inputSecond_GSI');
        $maxLas = Request::input('inputMaxLas');
        $mon = Request::input('inputMon');
        $tue = Request::input('inputTue');
        $wed = Request::input('inputWed');
        $thu = Request::input('inputThu');
        $fri = Request::input('inputFri');
        $sat = Request::input('inputSat');
        $sun = Request::input('inputSun');
        $start_time = Request::input('inputStartTime');
        $end_time = Request::input('inputEndTime');
        if ($second_gsi != -1) {
            $validator = Validator::make([
                "sid" => $sid,
                "type" => $type,
                "location" => $location,
                "gsi" => $gsi,
                "second_gsi" => $second_gsi,
                "maxLas" => $maxLas,
                "mon" => $mon,
                "tue" => $tue,
                "wed" => $wed,
                "thu" => $thu,
                "fri" => $fri,
                "sat" => $sat,
                "sun" => $sun,
                "start_time" => $start_time,
                "end_time" => $end_time,
            ], [
                "sid" => "required|exists:sections,id",
                "type" => "required|exists:types,id",
                "gsi" => "required|exists:passwords,gsi",
                "second_gsi" => "required_with:gsi|different:gsi|exists:passwords,gsi",
                "maxLas" => "required|integer|min:-1",
                "location" => "required",
                "mon" => "in:,0,1",
                "tue" => "in:,0,1",
                "wed" => "in:,0,1",
                "thu" => "in:,0,1",
                "fri" => "in:,0,1",
                "sat" => "in:,0,1",
                "sun" => "in:,0,1",
                "start_time" => "required",
                "end_time" => "required",
            ], [
                "sid.required" => "You are not editing a valid section.",
                "sid.exists" => "You are not editing a valid section.",
                "type.required" => "Please select a section type.",
                "type.exists" => "That does not appear to be a valid section type.",
                "gsi.required" => "Please select a GSI.",
                "gsi.exists" => "That does not appear to be a valid GSI.",
                "second_gsi.exists" => "That does not appear to be a valid GSI.",
                "second_gsi.different" => "You may not choose the same GSI as the second GSI. Simply leave the second GSI field empty.",
                "maxLas.required" => "Please enter the maximum amount of lab assistants for this section.",
                "maxLas.integer" => "The max amount of lab assistants needs to be an integer value.",
                "maxLas.min" => "Please enter a max lab assistants integer value equal to or greater than -1",
                "location.required" => "Please enter a location for the section."
            ]);
        }
        else {
            $validator = Validator::make([
                "sid" => $sid,
                "type" => $type,
                "location" => $location,
                "gsi" => $gsi,
                "second_gsi" => $second_gsi,
                "maxLas" => $maxLas,
                "mon" => $mon,
                "tue" => $tue,
                "wed" => $wed,
                "thu" => $thu,
                "fri" => $fri,
                "sat" => $sat,
                "sun" => $sun,
                "start_time" => $start_time,
                "end_time" => $end_time,
            ], [
                "sid" => "required|exists:sections,id",
                "type" => "required|exists:types,id",
                "gsi" => "required|exists:passwords,gsi",
                "maxLas" => "required|integer|min:-1",
                "location" => "required",
                "mon" => "in:,0,1",
                "tue" => "in:,0,1",
                "wed" => "in:,0,1",
                "thu" => "in:,0,1",
                "fri" => "in:,0,1",
                "sat" => "in:,0,1",
                "sun" => "in:,0,1",
                "start_time" => "required",
                "end_time" => "required",
            ], [
                "sid.required" => "You are not editing a valid section.",
                "sid.exists" => "You are not editing a valid section.",
                "type.required" => "Please select a section type.",
                "type.exists" => "That does not appear to be a valid section type.",
                "gsi.required" => "Please select a GSI.",
                "gsi.exists" => "That does not appear to be a valid GSI.",
                "second_gsi.exists" => "That does not appear to be a valid GSI.",
                "second_gsi.different" => "You may not choose the same GSI as the second GSI. Simply leave the second GSI field empty.",
                "maxLas.required" => "Please enter the maximum amount of lab assistants for this section.",
                "maxLas.integer" => "The max amount of lab assistants needs to be an integer value.",
                "maxLas.min" => "Please enter a max lab assistants integer value equal to or greater than -1",
                "location.required" => "Please enter a location for the section."
            ]);
        }
        //Route our validator
        if ($validator->fails()) {
            return redirect()->route('taconsole')->withErrors($validator);
        }
        //Create a new instance of our Section model
        $section = Section::findOrFail($sid);
        $section->type = $type;
        $section->location = $location;
        $section->gsi = $gsi;
        $section->second_gsi = $second_gsi;
        $section->max_las = $maxLas;
        //Yes someone who reads this and thinks it looks as
        //awful as I do. Please make a PR and fix this before
        //I get too mad looking at it and come up with a more
        //elegant solution using a loop. :P
        if ($mon != 1)
            $section->mon = 0;
        else
            $section->mon = 1;
        if ($tue != 1)
            $section->tue = 0;
        else
            $section->tue = 1;
        if ($wed != 1)
            $section->wed = 0;
        else
            $section->wed = 1;
        if ($thu != 1)
            $section->thu = 0;
        else
            $section->thu = 1;
        if ($fri != 1)
            $section->fri = 0;
        else
            $section->fri = 1;
        if ($sat != 1)
            $section->sat = 0;
        else
            $section->sat = 1;
        if ($sun != 1)
            $section->sun = 0;
        else
            $section->sun = 1;
        $section->start_time = $start_time;
        $section->end_time = $end_time;
        //Push to the DB
        $section->save();
        //Return a redirect Response
        return redirect()->route("taconsole")->with("message", "The section was edited successfully.");
    }

    public function post_section_assign() {
        $uid = Request::input('inputUID');
        $section = Request::input('inputSID');
        $assignment = Assignment::where("uid", "=", $uid)->where("section", "=", $section)->count();
        $u = User::findOrFail($uid);
        $s = Section::findOrFail($section);
        if ($assignment == 0) {
            $assignment = new Assignment;
            $assignment->uid = $uid;
            $assignment->section = $section;
            $assignment->save();
        }
        return "1";
    }

    public function post_section_unassign() {
        $uid = Request::input('inputUID');
        $section = Request::input('inputSID');
        $assignment = Assignment::where("uid", "=", $uid)->where("section", "=", $section)->first();
        $assignment->delete();
        return "1";
    }

}
