<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\Sequence;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $from = Carbon::now()->subMonths(1)->firstOfMonth();
      // $from = Carbon::now()->subDays(5)->startOfDay();
      $to = Carbon::now()->subDays(1);
      $period = CarbonPeriod::create($from, '1 day', $to);

      \App\Models\Device::find(1)->employees
      ->where('start', '<=', $from)
      ->where('end', '>=', $to)
      ->take(10)->each(function($employee) use($period) {
        foreach ($period as $key => $day) {

          $calendar = \App\Models\Calendar::create([
          'device_id' => 1,
          'employee_id' => $employee->id,
          'user_id' => 1,
          'date' => $day,
          ]);

          $rooms = \App\Models\Room::where('device_id', 1)->inRandomOrder()->take(10)->get();
          for ($i=0; $i < 10; $i++) {
            // Create Pivot with Parameters
            $calendar->rooms()->attach(
              $rooms[$i]->id, [
              'clean_type' =>  random_int(0,1),
              'extra' =>  random_int(0,3),
              'status' => $i%3==0 ? 3 : random_int(0,2),
              'volunteer' => $i%3==0 ? \App\Models\Employee::where('device_id', 1)->inRandomOrder()->limit(1)->first()->id : null,
              // 'time_cleaned' => Carbon::now()->addHours(4)->toDateTimeString(),
              // 'time_cleaned' => function() use ($day) {return $day->addHours(4)->toDateTimeString();},
            ]);
          }
          
        }
      });
      
    }
}
