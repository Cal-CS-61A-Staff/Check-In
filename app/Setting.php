<?php namespace App;

use Auth, Request;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Setting extends Model
{

    protected $table = 'settings';

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Los_Angeles')
           ->toDateTimeString();
    }

    public static function getValue($name)
    {
        return Setting::where("name", "=", $name)->firstOrFail()->value;
    }
    
    public static function change($name, $value)
    {
        $setting = Setting::where("name", "=", $name)->firstOrFail();
        $setting->value = $value;
        $setting->save();
    }
    
}
