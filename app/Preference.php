<?php namespace App;

use Auth, Request;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Preference extends Model
{

    protected $table = 'preferences';

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Los_Angeles')
            ->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Los_Angeles')
            ->toDateTimeString();
    }

    public function sec() {
        return $this->hasOne('App\Section', "id", "section");
    }

    public function user() {
        return $this->hasOne('App\User', "id", "uid");
    }
}