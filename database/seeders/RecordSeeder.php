<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\Sequence;

class RecordSeeder extends Seeder
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

          $perform = array_rand( config('constants.performs') );

          \App\Models\Record::factory()
            ->count(4)
            ->state(new Sequence(
              [
                'time' => function() use ($day, $key) {return $day->addHours(8)->addMinutes($key)->toDateTimeString();},
                'perform' => $perform,
                'action' => 0 //checkin
              ],
              [
                // 'time' => $time->addHours(4),
                'time' => function() use ($day) {return $day->addHours(4)->toDateTimeString();},
                'perform' => $perform,
                'action' => 2 //pause in
              ],
              [
                // 'time' => $time->addMinutes(15),
                'time' => function() use ($day) {return $day->addMinutes(15)->toDateTimeString();},
                'perform' => $perform,
                'action' => 3 //pause out
              ],
              [
                // 'time' => $time->addHours(3),
                'time' => function() use ($day) {return $day->addHours(3)->toDateTimeString();},
                'perform' => $perform,
                'action' => 1 //checkout
              ],
            ))
            ->create(['employee_id' => $employee->id])
            // ->each(function ($record) {
            //   if ($record->action == 1) {
            //     $rooms = \App\Models\Room::where('device_id', 1)->inRandomOrder()->take(10)->get();
            //     for ($i=1; $i < 10; $i++) {
            //       // $room = \App\Models\Room::where('device_id', 1)->inRandomOrder()->first();
            //       // $room = \App\Models\Room::find($i);
            //       // Create Pivot with Parameters
            //       $record->rooms()->attach(
            //         $rooms[$i]->id,[
            //         // 'clean_type' =>  random_int(0,1),
            //         // 'extra' =>  random_int(0,3),
            //       ]);
            //     }
            //   }
            // });
            ->each(function ($record) use($day, $employee) {
              if ($record->action == 1) {
                $calendar = \App\Models\Calendar::where('date', $day->toDateString())
                ->where('employee_id', $employee->id)->first();
                // $this->command->info($calendar);
                if ( $calendar ) {
                  $record->calendar_id = $calendar->id;
                  $record->save();
                  $rooms = $calendar->rooms()->inRandomOrder()->take(8)->get();
                  foreach ($rooms as $key => $room) {
                    $room->pivot->record_id = $record->id;
                    $room->pivot->save();
                  }
                }
              }
            });
  
        }
      });
      
    }
}
