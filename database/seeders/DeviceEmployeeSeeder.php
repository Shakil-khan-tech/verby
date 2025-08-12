<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DeviceEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      \App\Models\Employee::all()->each( function ($employee) {
        $locations = array_map('intval', explode(',', $employee->plani));
        // array_push( $locations, $employee->device_id );
        $employee->devices()->sync( array_unique( array_filter($locations) ) );
      });

      // \App\Models\Employee::whereNotNull('plani')
      // ->where('plani','<>','')
      // ->get()->each( function ($employee) {
      //   $employee->devices()->sync($employee->plani);
      // });
    }
}
