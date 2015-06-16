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


}
