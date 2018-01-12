<?php

use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    /**
     * Seed the Settings table with the necessary default rows.
     * These values can be changed once logged in from the TA 
     * Console. 
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
        	'name' => 'allow_section_signups',
        	'value' => 0,
        ]);

        DB::table('settings')->insert([
        	'name' => 'information_content',
        	'value' => '',
        ]);
    }
}
