<?php

namespace Database\Seeders\Temp;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class FixSymbolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Fixing symbols...');
        Plan::where('symbol', 'U')->update(['symbol' => 'A']);
        Plan::where('symbol', 'UN')->update(['symbol' => 'U']);
        $this->command->info('Done!');
    }
}
