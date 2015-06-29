<?php namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model {

    protected $tabled = 'announcements';

    public function user() {
        return $this->hasOne('App\User', 'id', 'author');
    }

}



