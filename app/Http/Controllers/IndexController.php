<?php namespace App\Http\Controllers;

use Auth, Request, Validator, Hash;
use App\User;
use App\Audit;
use Illuminate\Routing\Controller;

class IndexController extends Controller {

    public function get_index()
    {
        //Is the member logged in?
        if (Auth::check())
        {
            return redirect()->route('lacheckin');
        }
        //They are not, let's redirect to the login page
        return redirect()->route('login');
    }

    public function get_login()
    {
        //Return our login view
        return view('login');
    }

    public function get_logout()
    {
        //Log the user out
        Auth::logout();
        //Redirect back to the index with a message
        return redirect()->route("login")->with("message", "You have been successfully logged out.");
    }

    public function get_registration()
    {
        //Return our registration view
        return view('registration');
    }

    public function post_registration()
    {
        //Get all of our form input
        $input = Request::all();
        //Start our validator
        $validator = Validator::make([
            "name" => $input["inputName"],
            "sid" => $input["inputSID"],
            "email" => $input["inputEmail"],
            "password" => $input["inputPassword"],
        ], [
            "name" => "required",
            "sid" => "required|integer",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:8",
        ], [
            "name.required" => "Please enter your full first and last name.",
            "sid.required" => "Please enter your numeric student ID. You can find it on your Cal One Card.",
            "sid.integer" => "Your student ID should be numeric.",
            "email.required" => "Please enter your email.",
            "email.email" => "That does not appear to be a valid email address.",
            "password.required" => "You must enter a password.",
            "password.min" => "Your password must be at least 8 characters",
        ]);

        if ($validator->fails())
        {
            //We have issues. Redirect back to the form with all of the input except the password and include the error messages
            return redirect()->back()->withErrors($validator->errors())->withInput(Request::except('inputPassword'));
        }
        //Alright all of the validation is complete. Now to create our new user
        $user = new User;
        $user->name = $input["inputName"];
        $user->sid = $input["inputSID"];
        $user->email = $input["inputEmail"];
        //Hash our password
        $hashedpasswd = Hash::make($input["inputPassword"]);
        $user->password = $hashedpasswd;
        //Save our model to the database
        $user->save();

        //Log the user in

        Auth::loginUsingId($user->id);
        //Create an audit log entry for this action
        Audit::log("Account created.");

        //Redirect them to the checkin page with the following message
        return redirect()->route("lacheckin")->with("message", "Thanks " . $user->name . ", your account was successfully created. You can now check in to your lab sections using your credentials.");
    }


}
