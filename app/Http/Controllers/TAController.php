<?php namespace App\Http\Controllers;

use Response, Auth, Request;
use Illuminate\Routing\Controller;
use App\Checkin;
use App\User;
use App\Password;

class TAController extends Controller {

    public function get_console() {
        //Get all of our checkins
        $checkins = Checkin::with("ta")->with("type")->with("user")->orderBy("created_at", "DESC")->get();
        //Get all of our users
        $users = User::orderBy("name", "ASC")->get();
        //Get our password
        $password = Password::where("gsi", "=", Auth::user()->id)->first()->password;
        return view("ta.console")->with(["checkins" => $checkins, "users" => $users, "password" => $password]);
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
