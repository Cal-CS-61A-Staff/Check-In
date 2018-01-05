<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Carbon\Carbon;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Los_Angeles')
            ->toDateTimeString();
    }

    public static function get_assignedHours($users) {
        $assigned_hours = [];
        foreach ($users as $user) {
            if (!array_key_exists($user->id, $assigned_hours)) {
                $assigned_hours[$user->id] = 0;
            }
            foreach ($user->assignments as $assignment)
                $assigned_hours[$user->id] += $assignment->sec->category->hours;
        }
        return $assigned_hours;
    }

    public static function get_underHours($users, $assigned_hours) {
        $under_hours = [];
        foreach ($users as $user) {
            if ($assigned_hours[$user->id] + 0.5 < $user->hours) {
                $under_hours[$user->id] = $assigned_hours[$user->id] - $user->hours;
            }
        }
        return $under_hours;
    }

    public static function get_overHours($users, $assigned_hours) {
        $over_hours = [];
        foreach ($users as $user) {
            if ($assigned_hours[$user->id] > $user->hours) {
                $over_hours[$user->id] = $assigned_hours[$user->id] - $user->hours;
            }
        }
        return $over_hours;
    }

    public static function get_doubleBooked($assignments) {
        $users = [];
        $double_booked = [];
        foreach ($assignments as $assignment) {
            $days = Section::daysToString($assignment->sec);
            if (!array_key_exists($assignment->uid, $double_booked)) {
                $double_booked[$assignment->uid] = [$days => [$assignment->sec->start_time => $assignment->sec->start_time ]];
            }
            else if (!array_key_exists($days, $double_booked[$assignment->uid])) {
                $double_booked[$assignment->uid][$days] = [$assignment->sec->start_time => $assignment->sec->start_time];
            }
            else if (array_key_exists($assignment->sec->start_time, $double_booked[$assignment->uid][$days])) {
                $users[$assignment->uid] = $assignment->uid;
            }
            else {
                $double_booked[$assignment->uid][$days][$assignment->sec->start_time] = $assignment->sec->start_time;
            }
        }
        return $users;
    }

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

    public function assignments() {
        return $this->hasMany('App\Assignment', 'uid');
    }

    public function checkins() {
        return $this->hasMany('App\Checkin', 'uid');
    }

    public function is_gsi() {
        if ($this->access == 1)
            return true;
        return false;
    }

    public function is_tutor() {
        if ($this->access == 0.5)
            return true;
        return false;
    }

    public function is_staff() {
        if ($this->access > 0)
            return true;
        return false;
    }


}
