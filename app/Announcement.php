<?php namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Announcement extends Model {

    protected $tabled = 'announcements';

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Los_Angeles')
            ->toDateTimeString()
            ;
    }

    public function user() {
        return $this->hasOne('App\User', 'id', 'author');
    }

}



