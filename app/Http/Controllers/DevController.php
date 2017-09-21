<?php namespace App\Http\Controllers;

use Auth;

class DevController extends Controller {

    public function get_login($id) {
        Auth::loginUsingId($id);
        return redirect()->route("index");
    }
    
}

