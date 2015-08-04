<?php namespace App\Http\Controllers;

use App\Announcement;
use Response, Auth, Request, Validator, View;
use Illuminate\Routing\Controller;
use App\Checkin;
use App\User;
use App\Audit;
use App\Type;
use App\Password;

class TAController extends Controller {

    public function __construct() {
        $announcements = Announcement::where("hidden", "!=", 0)->orderBy("created_at", "DESC")->get();
        View::share('announcements', $announcements);
    }
    public function get_console() {
        //Get all of our checkins
        $checkins = Checkin::with("ta")->with("type")->with("user")->orderBy("created_at", "DESC")->get();
        //Get all of our users
        $users = User::orderBy("name", "ASC")->get();
        //Get our password
        $password = Password::where("gsi", "=", Auth::user()->id)->first()->password;
        //Get our gsis
        $gsis = User::where('access', '>', 0)->get();
        //Get our types
        $types = Type::all();
        //Get our audits
        $audits = Audit::with("user")->orderBy('created_at', 'DESC')->get();
        //Get our announcements
        $announcements = Announcement::with("user")->orderBy("hidden", "DESC")->orderBy("created_at", "DESC")->get();
        return view("ta.console")->with(["audits" => $audits, "announcements_ta" => $announcements, "gsis" => $gsis, "types" => $types, "checkins" => $checkins, "users" => $users, "password" => $password]);
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
        //Alright all good let's update the model
        $type->name = $name;
        $type->hidden = $hidden;
        //And finally the DB
        $type->save();
        //Let the user know things are well :)
        return redirect()->route("taconsole")->with("message", "The type was updated to " . $name);
    }


    public function post_new_type() {
        //Get our name
        $name = Request::input("inputName");
        $hidden = Request::input("inputHidden");
        if ($hidden != 1)
            $hidden = 0;
        $otherTypes = Type::where("name", "=", $name)->count();
        if ($otherTypes > 0 ||$name == "")
            return redirect()->route("taconsole")->with("message", "Either your new event type is empty or an existing event type exists with the same name.");
        //Create our model
        $type = new Type;
        $type->name = $name;
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
        fputcsv($handle, array('Name', 'Type', 'Date', 'Start Time', 'GSI', 'Makeup', 'Logged At'));
        foreach($checkins as $checkin) {
            if ($checkin->makeup == 1)
                $makeup = "Yes";
            else
                $makeup = "No";
            fputcsv($handle, array($checkin->user->name, $checkin->type->name, $checkin->date, $checkin->time, $checkin->ta->name, $makeup, $checkin->created_at));
        }
        fclose($handle);
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        return Response::download($file, 'checkins.csv', $headers);
    }

    public function get_download_roster() {
        $users = User::with("checkins")->orderBy("name", "ASC")->get();
        $file = storage_path() . "/app/roster.csv";
        $handle = fopen($file, 'w+');
        fputcsv($handle, array('Name', 'Email', 'GSI', 'Total # of Check Ins', 'Created At'));
        foreach($users as $user) {
            if ($user->access > 0)
                $gsi = "YES";
            else
                $gsi = "No";
            fputcsv($handle, array($user->name, $user->email, $gsi, count($user->checkins), $user->created_at));
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

}
