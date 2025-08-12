<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Plan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\Sequence;

class PlanSeeder extends Seeder
{
    protected $needle = 0;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $from = Carbon::now()->subDays(7);
      $to = Carbon::now()->subDays(1);
      $period = CarbonPeriod::create($from, '1 day', $to);

      Employee::all()->each(function ($employee) use($period) {

        foreach ($period as $key => $day) {
          $perform = array_rand( config('constants.performs') );
          Plan::factory()
            ->count(1)
            ->state(new Sequence(
              [
                'dita' => $day->toDateString(),
              ],
            ))
            ->create([
              'employee_id' => $employee->id,
              'device_id' => $employee->devices->random()->id,
            ]);
        }
        
      });

    }
}