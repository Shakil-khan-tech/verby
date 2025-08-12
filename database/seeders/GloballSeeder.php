<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class GloballSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('globall')->insert([
          'AHV' => '5.275',
          'ALV' => '1.10',
          'NBUV' => '2.040',
          'KTG50' => '2.169',
          'VPK' => '0.00'
        ]);
    }
}
