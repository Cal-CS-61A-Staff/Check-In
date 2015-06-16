<?php namespace App\Http\Controllers;

use Auth;
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
        die (var_dump($input));
        //Start our validator
        $validator = Validator::make([
        ], [], []);
    }


}
