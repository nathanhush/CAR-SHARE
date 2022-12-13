<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'firstname' => 'Edet',
            'lastname' => 'Bobby',
            'phone' => '07033310715',
            'role_id' => 1,
            'email' => 'bobby@gmail.com',
            'password' => Hash::make('Boondocks1!')
        ]);

        DB::table('users')->insert([
            'firstname' => 'Tobi',
            'lastname' => 'Ijaware',
            'phone' => '07033310705',
            'role_id' => 1,
            'email' => 'oluwatobiijaware@gmail.com',
            'password' => Hash::make('Boondocks1!')
        ]);
    }
}
