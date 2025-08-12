<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCalendarRoom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_room', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0); //0-uncleaned, 1-cleaned, 2-red card, 3-volunteer
            $table->unsignedBigInteger('volunteer')->nullable(); //volunteer
            $table->unsignedBigInteger('record_id')->nullable(); //record

            $table->foreign('volunteer')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('record_id')->references('id')->on('records')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendar_room', function (Blueprint $table) {
            //
        });
    }
}
