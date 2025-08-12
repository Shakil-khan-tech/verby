<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DBCleanupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Schema::table('employees', function (Blueprint $table){
        if (Schema::hasColumn('employees', 'old_id')) {
          $table->dropColumn('old_id');
        }
        if (Schema::hasColumn('employees', 'device_id')) {
          $table->dropColumn('device_id');
        }
        if (Schema::hasColumn('employees', 'plani')) {
          $table->dropColumn('plani');
        }
      });
      Schema::table('lohnabrechnung', function (Blueprint $table){
        if (Schema::hasColumn('lohnabrechnung', 'old_id')) {
          $table->dropColumn('old_id');
        }
      });
      Schema::table('lohnabrechnung_revisions', function (Blueprint $table){
        if (Schema::hasColumn('lohnabrechnung_revisions', 'old_id')) {
          $table->dropColumn('old_id');
        }
      });
      Schema::table('plani', function (Blueprint $table){
        if (Schema::hasColumn('plani', 'old_id')) {
          $table->dropColumn('old_id');
        }
      });
      Schema::table('pushimi', function (Blueprint $table){
        if (Schema::hasColumn('pushimi', 'old_id')) {
          $table->dropColumn('old_id');
        }
      });
      Schema::table('records', function (Blueprint $table){
        if (Schema::hasColumn('records', 'old_id')) {
          $table->dropColumn('old_id');
        }
      });
    }
}
