<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('holidays')->insert([
          [
            'name' => 'Neujahr',
            'month_day' => '01-01',
          ],
          [
            'name' => 'Karfreitag',
            'month_day' => '04-02',
          ],
          [
            'name' => 'Ostermontag',
            'month_day' => '04-05',
          ],
          [
            'name' => 'Auffahrt',
            'month_day' => '05-13',
          ],
          [
            'name' => 'Pfingstmontag',
            'month_day' => '05-24',
          ],
        ]);
    }
}
