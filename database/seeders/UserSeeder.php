<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Illuminate\Support\Str;

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
          [
            'name' => 'Jetmir Amiti',
            'email' => 'jetmir.amiti@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('outlaw'),
            'is_device' => 0,
            'remember_token' => Str::random(10),
          ],
          [
            'name' => 'Daut Ahmeti',
            'email' => 'daut.ahmeti@aaab.ch',
            'email_verified_at' => now(),
            'password' => bcrypt('123456!'),
            'is_device' => 0,
            'remember_token' => Str::random(10),
          ]
        ]);
    }
}
