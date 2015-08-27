<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Type extends Model {

    protected $table = "types";

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Los_Angeles')
            ->toDateTimeString()
            ;
    }

}
