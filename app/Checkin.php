<?php namespace App;

use Auth, Request;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model {

    protected $table = "checkins";

    public function ta() {
        return $this->hasOne('App\User', 'id', 'gsi');
    }

    public function type() {
        return $this->hasOne('App\Type', 'id', 'location');
    }

    public function user() {
        return $this->hasOne('App\User', 'id', 'uid');
    }

}

