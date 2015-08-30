<?php namespace App;

use Auth, Request;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Section extends Model {

    protected $table = 'sections';

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Los_Angeles')
            ->toDateTimeString()
            ;
    }

    public static function daysToString($s) {
        $dayString = "";
        if ($s->mon == 1)
            $dayString .= "Mon, ";
        if ($s->tue == 1)
            $dayString .= "Tue, ";
        if ($s->wed == 1)
            $dayString .= "Wed, ";
        if ($s->thu == 1)
            $dayString .= "Thu, ";
        if ($s->fri == 1)
            $dayString .= "Fri, ";
        if ($s->sat == 1)
            $dayString .= "Sat, ";
        if ($s->sun == 1)
            $dayString .= "Sun, ";
        if (strlen($dayString) > 2)
            $dayString = substr($dayString, 0, -2);
        return $dayString;
    }

    public function category() {
        return $this->hasOne('App\Type', 'id', 'type');
    }

    public function ta() {
        return $this->hasOne('App\User', 'id', 'gsi');
    }

    public function ta2() {
        return $this->hasOne('App\User', 'id', 'second_gsi');
    }

    public function pref() {
        return $this->hasMany('App\Preference', 'section', 'id');
    }

    public function assigned() {
        return $this->hasMany('App\Assignment', 'section', 'id');
    }

}
