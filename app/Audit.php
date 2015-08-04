<?php namespace App;

use Auth, Request;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model {

    protected $table = 'audits';

    public static function log($action) {
        $uid = Auth::user()->id;
        $ip = Request::ip();
        $audit = new Audit;
        $audit->uid = $uid;
        $audit->action = $action;
        $audit->ip = $ip;
        //Save our new audit
        $audit->save();
    }

    public function user() {
        return $this->hasOne('App\User', 'id', 'uid');
    }

}
