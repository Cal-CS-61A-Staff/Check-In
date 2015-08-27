<?php namespace App;

use Auth, Request;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Password extends Model {

    protected $table = "passwords";

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Los_Angeles')
            ->toDateTimeString()
            ;
    }

}
