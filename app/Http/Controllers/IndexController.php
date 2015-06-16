<?php namespace App\Http\Controllers;

use Auth, Request, Validator;
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
            "email" => "required|email",
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
    }


}
