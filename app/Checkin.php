<?php namespace App;

use Auth, Request;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Checkin extends Model {

    protected $table = "checkins";


    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Los_Angeles')
            ->toDateTimeString()
            ;
    }

    public function ta() {
        return $this->hasOne('App\User', 'id', 'gsi');
    }

    public function type() {
        return $this->hasOne('App\Type', 'id', 'location');
    }

    public function user() {
        return $this->hasOne('App\User', 'id', 'uid');
    }

    public static function perWeek($checkins) {
        $years = array();
        foreach ($checkins as $checkin) {
            $date = new \DateTime($checkin->created_at);
            $year = $date->format("Y");
            $week = $date->format("W");
            if (!array_key_exists($year, $years)) {
                $years[$year] = array();
            }
            if (!array_key_exists($week, $years[$year])) {
                $years[$year][$week] = ["value" => date( "Y-m-d", strtotime($year."W".$week."1") ), "data" => 1];
            }
            else {
                $years[$year][$week]["data"] += 1;
            }
        }
        return $years;
    }

    public static function uniquePerWeek($checkins) {
        $years = array();
        foreach ($checkins as $checkin) {
            $date = new \DateTime($checkin->created_at);
            $year = $date->format("Y");
            $week = $date->format("W");
            if (!array_key_exists($year, $years)) {
                $years[$year] = array();
            }
            if (!array_key_exists($week, $years[$year])) {
                $years[$year][$week] = ["value" => date( "Y-m-d", strtotime($year."W".$week."1") ), "data" => 1, "users" => [$checkin->uid]];
            }
            else if (!in_array($checkin->uid, $years[$year][$week]["users"])) {
                $years[$year][$week]["users"][] = $checkin->uid;
                $years[$year][$week]["data"] += 1;
            }
        }
        return $years;
    }

    public static function perStaff($checkins) {
        $checkinsPerStaff = array();
        $staff = User::where("access", ">", 0)->orderBy("name", "ASC")->get();
        foreach ($staff as $s) {
            $checkinsPerStaff[$s->id] = ["name" => $s->name, "data" => 0];
        }
        foreach ($checkins as $checkin) {
           $checkinsPerStaff[$checkin->gsi]["data"] += 1;
        }
        return $checkinsPerStaff;
    }


    public static function userHours($checkins) {
        $user_hours = array();
        $users = User::all();
        foreach ($users as $user) {
            $user_hours[$user->id] = 0;
        }
        foreach ($checkins as $c) {
            $user_hours[$c->uid] += $c->type->hours;
        }
        return $user_hours;
    }

}

