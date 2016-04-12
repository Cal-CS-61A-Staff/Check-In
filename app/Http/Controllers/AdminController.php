<?php namespace App\Http\Controllers;

use App\Type;
use App\User;
use App\Checkin;

class AdminController extends Controller {
    
    public function get_import_checkins() {
        $fileName = "week_0_checkins.csv";
        $filePath = base_path() . "/dumps/" . $fileName;
        $delimiter = ',';
        ini_set('auto_detect_line_endings',TRUE);
        if(!file_exists($filePath) || !is_readable($filePath))
            return FALSE;
        $header = NULL;
        $data = array();
        if (($handle = fopen($filePath, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if (!$header) {
                    $header = $row;
                }
                else {
                    if (count($header) > count($row)) {
                        $difference = count($header) - count($row);
                        for ($i = 1; $i <= $difference; $i++) {
                            $row[count($row) + 1] = $delimiter;
                        }
                    }
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        foreach ($data as $checkin) {
            $default_gsi_email = "cschoen@berkeley.edu";
            $location = Type::where("name", "=", "Lab")->firstOrFail()->id;
            $email = $checkin["Lab Assistant Email"];
            $gsi_email = $checkin["Username"];
            $created_at = explode(" ", $checkin["Timestamp"]);
            $timestamp = strtotime($checkin["Timestamp"]);
            $date = date("j F, Y", $timestamp);
            $time = date("g:i A", $timestamp);
            try {
                $user = User::where("email", "=", $email)->firstOrFail();
            } catch (\Exception $e)  {
                echo "<br />";
                echo "Could not find user " . $email . ", skipping ...";
                continue;
            }
            try {
                $gsi = User::where("email", "=", $gsi_email)->firstOrFail();
            } catch (\Exception $e) {
                echo "<br />";
                echo "Could not find GSI " . $gsi_email . ". Using cschoen@berkeley.edu.";
                $gsi = User::where("email", "=", $default_gsi_email)->firstOrFail();
            }
            //Create the checkin
            $checkin = new Checkin;
            $checkin->uid = $user->id;
            $checkin->gsi = $gsi->id;
            $checkin->location = $location;
            $checkin->date = $date;
            $checkin->time = $time;
            $checkin->makeup = 0;
            $checkin->save();
            echo "<br />";
            echo "Checkin created for " . $email;
        }
    }
}

