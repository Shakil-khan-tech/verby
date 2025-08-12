<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Sequence;

class EmployeeSeeder extends Seeder
{
    protected $needle = 0;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Employee::factory()
          ->count(20)
          ->create();
    }
}
