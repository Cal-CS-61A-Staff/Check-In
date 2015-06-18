<?php namespace App\Http\Controllers;

use Response, Auth, Request, Validator;
use Illuminate\Routing\Controller;
use App\Checkin;
use App\User;
use App\Audit;
use App\Type;
use App\Password;

class TAController extends Controller {

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
        return view("ta.console")->with(["gsis" => $gsis, "types" => $types, "checkins" => $checkins, "users" => $users, "password" => $password]);
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

    public function get_user_promote($id) {
        $user = User::findOrFail($id);
        if ($user->access > 0)
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
        return redirect()->route("taconsole")->with("message", $user->name . " successfully promoted from Lab Assistant to GSI.");
    }

    public function get_user_demote($id) {
        $user = User::findOrFail($id);
        if ($user->access == 0)
            return redirect()->route("taconsole")->with("message", "This user is not currently a TA and has only lab assistant permissions.");
        //Ok promote the user
        $user->access = 0;
        $user->save();
        //Create an audit log for this
        Audit::log("Demoted from GSI to Lab Assistant by " . Auth::user()->name);
        //Delete their password
        $password = Password::where("gsi", "=", $user->id)->first();
        $password->delete();
        //Let them know all is ok
        return redirect()->route("taconsole")->with("message", $user->name . " successfully demoted from GSI to Lab Assistant.");
    }

    public function get_download_checkins() {
        $checkins = Checkin::with("ta")->with("type")->with("user")->orderBy("created_at", "DESC")->get();
        $file = storage_path() . "/app/checkins.csv";
        $handle = fopen($file, 'w+');
        fputcsv($handle, array('SID', 'Name', 'Type', 'Date', 'Start Time', 'GSI', 'Makeup', 'Logged At'));
        foreach($checkins as $checkin) {
            if ($checkin->makeup == 1)
                $makeup = "Yes";
            else
                $makeup = "No";
            fputcsv($handle, array($checkin->user->sid, $checkin->user->name, $checkin->type->name, $checkin->date, $checkin->time, $checkin->ta->name, $makeup, $checkin->created_at));
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
        fputcsv($handle, array('SID', 'Name', 'Email', 'GSI', 'Total # of Check Ins', 'Created At'));
        foreach($users as $user) {
            if ($user->access > 0)
                $gsi = "YES";
            else
                $gsi = "No";
            fputcsv($handle, array($user->sid, $user->name, $user->email, $gsi, count($user->checkins), $user->created_at));
        }
        fclose($handle);
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        return Response::download($file, 'roster.csv', $headers);
    }

}
