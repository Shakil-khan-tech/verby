<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
// use DateTime;
// use Illuminate\Database\Eloquent\Factories\Sequence;
// use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(1)->create();
        $this->call(UserSeeder::class);

        $this->call(GloballSeeder::class);
        $this->call(HolidaySeeder::class);

        $this->call(DeviceSeeder::class);
        \App\Models\Room::factory(1000)->create();
        
        
        $emp_path = '.delete/tables/employees.sql';
        DB::unprepared(file_get_contents( $emp_path ));
        $this->command->info('Employees table seeded!');
        
        $this->call(DeviceEmployeeSeeder::class);
        
        $pushimi_path = '.delete/tables/pushimi.sql';
        DB::unprepared(file_get_contents( $pushimi_path ));
        $this->command->info('Pushimi table seeded!');

        $lohn_path = '.delete/tables/lohnabrechnung.sql';
        DB::unprepared(file_get_contents( $lohn_path ));
        $this->command->info('Lohnabrechnung table seeded!');

        $lohnrev_path = '.delete/tables/lohnabrechnung_revisions.sql';
        DB::unprepared(file_get_contents( $lohnrev_path ));
        $this->command->info('Lohnabrechnung Revisions table seeded!');

        $plan_path = '.delete/tables/plani.sql';
        DB::unprepared(file_get_contents( $plan_path ));
        $this->command->info('Plani table seeded!');

        $this->call(CalendarSeeder::class);
        
        $this->call(RecordSeeder::class);

        $this->call(PermissionSeeder::class);

        $this->call(DBCleanupSeeder::class);
    }
}
