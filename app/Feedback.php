<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Feedback extends Model {

    protected $table = "feedback";

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Los_Angeles')
            ->toDateTimeString()
            ;
    }

}
