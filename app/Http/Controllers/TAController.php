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
use App\Preference;
use App\Setting;
use App\Feedback;

class TAController extends Controller {

    public function __construct() {
        $announcements = Announcement::where("hidden", "!=", 0)->orderBy("created_at", "DESC")->get();
        View::share('announcements', $announcements);
    }

    public function get_console($module = null) {
        if (empty($module)) {
            return redirect()->route("taconsole", "users");
        }
        return view("ta.console")->with(["module" => $module]);
    }

    public function get_module_users() {
        $users = User::orderBy("name", "ASC")->get();
        $checkins = Checkin::with("type")->get();
        $types = Type::all();
        $staff = $users->filter(function ($user) {
            return $user->is_staff();
        });
        $user_hours = Checkin::userHours($checkins, $users);
        $assigned_hours = User::get_assignedHours($users);
        //Get under hours
        $under_hours = User::get_underHours($users, $assigned_hours);
        //Get over hours
        $over_hours = User::get_overHours($users, $assigned_hours);

        return view("ta.modules.users")->with([
            "users" => $users,
            "staff" => $staff,
            "user_hours" => $user_hours,
            "types" => $types]);
    }

    public function get_module_checkins() {
        $checkins = Checkin::with("ta")->with("type")->with("user")->orderBy("created_at", "ASC")->get();
        $types = Type::all();
        $staff = User::where("access", ">", "0")->orderBy("name")->get();
        return view("ta.modules.checkins")->with(["checkins" => $checkins, "staff" => $staff, "types" => $types]);
    }

    public function get_module_secretword() {
        $password = Password::where("gsi", "=", Auth::user()->id)->first()->password;
        return view("ta.modules.secretword")->with(["password" => $password]);
    }

    public function get_module_announcements() {
        $announcements = Announcement::with("user")->orderBy("hidden", "DESC")->orderBy("created_at", "DESC")->get();
        return view("ta.modules.announcements")->with(["announcements" => $announcements]);
    }

    public function get_module_export() {
        return view("ta.modules.export");
    }

    public function get_module_sections() {
        $sections = Section::with("assigned.user")->with("ta")->with("ta2")->with("category")->orderBy("type", "ASC")->get();
        //Get GSis lab assistants
        $yourLabAssistants = Section::with("assigned.user")->where("gsi", "=", Auth::user()->id)->orWhere("second_gsi", "=", Auth::user()->id)->get();
        $yourLabAssistantsEmails =  array();
        $yourLabAssistantsNames =  array();
        foreach ($yourLabAssistants as $ylas) {
            foreach($ylas->assigned as $ylas) {
                if (!in_array($ylas->user->email, $yourLabAssistantsEmails)) {
                    $yourLabAssistantsEmails[] = $ylas->user->email;
                    $yourLabAssistantsNames[] = $ylas->user->name;
                }
            }
        }

        $users = User::orderBy("name", "ASC")->get();
        $staff = $users->filter(function ($user) {
            return $user->is_staff();
        });
        $assigned_hours = User::get_assignedHours($users);
        $assignments = Assignment::with("sec")->with("user")->get();
        $double_booked = User::get_doubleBooked($assignments);
        //Get under hours
        $under_hours = User::get_underHours($users, $assigned_hours);
        //Get over hours
        $over_hours = User::get_overHours($users, $assigned_hours);
        $types = Type::all();

        User::where('access', '>', 0)->orderBy("name", "ASC")->get();
        return view("ta.modules.sections")->with([
            "yourLabAssistantsEmails" => $yourLabAssistantsEmails,
            "yourLabAssistantsNames" => $yourLabAssistantsNames,
            "double_booked" => $double_booked,
            "over_hours" => $over_hours,
            "under_hours" => $under_hours,
            "assigned_hours" => $assigned_hours,
            "sections" => $sections,
            "doubled_booked" => $double_booked,
            "types" => $types,
            "staff" => $staff
        ]);
    }

    public function get_module_stats() {
        $checkins = Checkin::with("ta")->with("user")->get();
        $checkins_per_week = Checkin::perWeek($checkins);
        $checkins_unique_per_week = Checkin::uniquePerWeek($checkins);
        $checkins_per_staff = Checkin::perStaff($checkins);
        return view("ta.modules.stats")->with([
            "checkins_per_week" => $checkins_per_week,
            "checkins_unique_per_week" => $checkins_unique_per_week,
            "checkins_per_staff" => $checkins_per_staff,
        ]);
    }

    public function get_module_eventtypes() {
        $types = Type::all();
        return view("ta.modules.eventtypes")->with([
            "types" => $types
        ]);
    }

    public function get_module_auditlog() {
        $audits = Audit::with("user")->orderBy('created_at', 'DESC')->get();
        return view("ta.modules.auditlog")->with([
            "audits" => $audits
        ]);
    }

    public function get_module_settings() {
        $allowSectionSignups = Setting::getValue("allow_section_signups");
        $informationContent = Setting::getValue("information_content");
        return view("ta.modules.settings")->with([
            "allowSectionSignups" => $allowSectionSignups,
            "informationContent" => $informationContent
        ]);
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
        return redirect()->route("taconsole", "secretWord")->with("message", "You have successfully updated your secret word");
    }

    public function post_edit_checkin_user() {
        $input = Request::all();
        //Start some validation
        $validator = Validator::make([
            "id" => $input["inputID"],
            "location" => $input["inputLocation"],
            "date" => $input["inputDate"],
            "time" => $input["inputTime"],
            "gsi" => $input["inputGSI"],
            "makeup" => $input["inputMakeup"],
        ], [
            "id" => "required|exists:checkins,id",
            "location" => "required|exists:types,id",
            "date" => "required",
            "time" => "required",
            "gsi" => "required|exists:passwords,gsi",
            "makeup" => "required|in:0,1",
        ], [
            "id.required" => "Invalid check in to edit",
            "id.exists" => "Invalid check in to edit",
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
        // Find our existing check in
        $checkin = Checkin::with("user")->findOrFail($input["inputID"]);
        $checkin->location = $input["inputLocation"];
        $checkin->date = $input["inputDate"];
        $checkin->time = $input["inputTime"];
        $checkin->gsi = $input["inputGSI"];
        $checkin->makeup = $input["inputMakeup"];
        //Save our new checkin
        $checkin->save();
        //Make an audit log entry for this checkin
        Audit::log("GSI " . Auth::user()->name . " edited existing checked in for " . $checkin->user->name);
        return redirect()->route("taconsole", "checkins")->with("message", "Editing check in for " . $checkin->user->name . " was successful.");
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
        return redirect()->route("taconsole", "users")->with("message", "Manual check in for " . $user->name . " was successful.");
    }

    public function post_checkin_remove($id) {
        //Does the checkin exists?
        $checkin = Checkin::with("user")->findOrFail($id);
        //Remove the checkin
        $checkin->delete();
        //Make an audit notation
        Audit::log(Auth::user()->name . " manually removed check in for " . $checkin->user->name);
        //Return our redirect
        return redirect()->route("taconsole", "checkins")->with("message", "Check in for " . $checkin->user->name . " successfully manually removed.");
    }

    public function get_user_promote_tutor($id) {
        $user = User::findOrFail($id);
        if ($user->is_gsi() || $user->is_tutor())
            return redirect()->route("taconsole")->with("message", "This user is already a TA or Tutor");
        //Promote the user to tutor
        $user->access = 0.5;
        $user->save();
        //Create an audit log for this
        Audit::log("Promoted " . $user->name . " from Lab Assistant to Tutor");
        //Set a default secret word for them
        $password = new Password;
        $password->gsi = $id;
        $password->password = "recursion";
        $password->save();
        //Let them know all is ok
        return redirect()->route("taconsole", "users")->with("message", $user->name . " successfully promoted from Lab Assistant to Tutor.");
    }

    public function get_user_promote($id) {
        $user = User::findOrFail($id);
        if ($user->is_gsi())
            return redirect()->route("taconsole")->with("message", "This user is already a TA.");
        //Ok promote the user
        $user->access = 1;
        $user->save();
        //Create an audit log for this
        Audit::log("Promoted " . $user->name . " from Lab Assistant to GSI");
        //Set a default secret word for them
        $password = new Password;
        $password->gsi = $id;
        $password->password = "recursion";
        $password->save();
        //Let them know all is ok
        return redirect()->route("taconsole", "users")->with("message", $user->name . " successfully promoted to GSI.");
    }

    public function get_user_demote($id) {
        $user = User::findOrFail($id);
        if ($user->access == 0)
            return redirect()->route("taconsole")->with("message", "This user is not currently a TA or Tutor and has only lab assistant permissions.");
        //Ok promote the user
        $user->access = 0;
        $user->save();
        //Create an audit log for this
        Audit::log("Demoted " . $user->name . " to Lab Assistant");
        //Delete their password
        $password = Password::where("gsi", "=", $user->id)->first();
        $password->delete();
        //Let them know all is ok
        return redirect()->route("taconsole", "users")->with("message", $user->name . " successfully demoted to Lab Assistant.");
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
            return redirect()->route("taconsole", "eventTypes")->with("message", "Either your type was empty or you entered an existing type.");
        if (!is_numeric($hours)) {
            return redirect()->route("taconsole", "eventTypes")->with("message", "Ensure you are entering a numeric value for hours in your event type.");
        }
        //Alright all good let's update the model
        $type->name = $name;
        $type->hours = $hours;
        $type->hidden = $hidden;
        //And finally the DB
        $type->save();
        //Let the user know things are well :)
        return redirect()->route("taconsole", "eventTypes")->with("message", "The " . $name . " type was updated successfully");
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
            return redirect()->route("taconsole", "eventTypes")->with("message", "Either your new event type is empty or an existing event type exists with the same name.");
        if (!is_numeric($hours)) {
            return redirect()->route("taconsole", "eventTypes")->with("message", "Ensure you are entering a numeric value for hours in your new event type.");
        }
        //Create our model
        $type = new Type;
        $type->name = $name;
        $type->hours = $hours;
        $type->hidden = $hidden;
        //Push the model to the DB
        $type->save();
        //Alert the user
        return redirect()->route("taconsole", "eventTypes")->with("message", "Your new event type was created successfully.");
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
        return redirect()->route('taconsole', "announcements")->with("message", "Your announcement was created. To make it public please change its visibility status.");
    }

    public function get_announcement_visibility($id) {
        $announcement = Announcement::findOrFail($id);
        if ($announcement->hidden == 0)
            $announcement->hidden = 1;
        else
            $announcement->hidden = 0;
        //Save our announcement
        $announcement->save();
        return redirect()->route('taconsole', "announcements")->with("message", "The announcement visibility was updated successfully");
    }

    public function get_announcement_delete($id) {
        $announcement = Announcement::findOrFail($id);
        //Delete it
        $announcement->delete();
        return redirect()->route('taconsole', "announcements")->with("message", "The announcement was deleted successfully");
    }

    public function post_section_import() {
        //Get our file
        $file = Request::file('inputSectionCSVFile');
        $file_path = $file->getPathName();
        $delimiter = ',';
        ini_set('auto_detect_line_endings',TRUE);
        if(!file_exists($file_path) || !is_readable($file_path))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($file_path, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if (!$header) {
                    $header = $row;
                }
                else {
                    if (count($header) > count($row)) {
                        $difference = count($header) - count($row);
                        for ($i = 1; $i <= $difference; $i++) {
                            $row[count($row) + 1] = $delimiter;
                        }
                    }
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        } 
        foreach ($data as $row) {
            $type = Type::where("name", "=", $row["Type"])->firstOrFail()->id;
            $location = $row["Location"];
            $gsi = User::where("email", "=", $row["GSI Email"])->firstOrFail()->id;
            $second_gsi = $row["Second GSI Email"];
            if (!empty($second_gsi))
                $second_gsi = User::where("email", "=", $second_gsi)->firstOrFail()->id;
            else
                $second_gsi = -1;
            $maxLas = $row["Max Lab Assistants"];
            $mon = $row["Monday"];
            $tue = $row["Tuesday"];
            $wed= $row["Wednesday"];
            $thu= $row["Thursday"];
            $fri = $row["Friday"];
            $sat = $row["Saturday"];
            $sun = $row["Sunday"];
            $start_time = $row["Start Time"];
            $end_time = $row["End Time"];
            $count = 0;
            $count++;
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
        }

        return redirect()->route("taconsole", "sections")->with("message", "Imported " . $count . " sections.");

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
        return redirect()->route("taconsole", "sections")->with("message", "The new section was successfully created.");
    }

    public function get_section_delete($sid) {
        $section = Section::findOrFail($sid);
        $assignments = Assignment::where('section', '=', $sid)->delete();
        $preferences = Preference::where('section', '=', $sid)->delete();
        //Delete the section
        $section->delete();
        return redirect()->route("taconsole", "sections")->with("message", "The section was successfully deleted.");
    }
    public function post_section_addla() {
        $section = Section::findOrFail(Request::input('inputSection'));
        $email= Request::input('inputEmail');
        $user = User::where("email", "=", $email)->firstOrFail();
        $assignment = new Assignment;
        $assignment->section = $section->id;
        $assignment->uid= $user->id;
        $assignment->save();
        //Log this
        Audit::log("Assigned " . $user->name . " to section id " . $section->id);
        return redirect()->route("taconsole", "sections")->with("message", $user->name . " successfully added to section.");
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
            return redirect()->route('taconsole', "sections")->withErrors($validator);
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
        return redirect()->route("taconsole", "sections")->with("message", "The section was edited successfully.");
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
        Audit::log("Removed " . $uid . " from section id " . $section);
        return "1";
    }

    public function post_settings_save() {
        $allowSectionSignups = Request::input('inputAllowSectionSignups');
        if ($allowSectionSignups == 1) {
           Setting::change("allow_section_signups", 1);
        }
        else {
           Setting::change("allow_section_signups", 0);
        }
        $informationContent = Request::input('inputInformationContent');
        Setting::change("information_content", $informationContent);
        return redirect()->route("taconsole", "settings")->with("message", "The settings were saved successfully.");
    }

    public function post_feedback_add() {
        $uid = Request::input('inputLA');
        // Ensure the user exists
        User::findOrFail($uid);
        $comment = Request::input('inputFeedback');
        $feedback = new Feedback;
        $feedback->uid = $uid;
        $feedback->gsi = Auth::user()->id;
        $feedback->comment = $comment;
        $feedback->save();
        return redirect()->route("taconsole", "users")->with("message", "Feedback successfully saved.");
    }

}
