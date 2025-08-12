<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendarRoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_room', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->unsigned()->index();
            $table->unsignedBigInteger('calendar_id')->unsigned()->index();
            $table->tinyInteger('clean_type'); //'Depa', 'Restant'
            $table->tinyInteger('extra'); //'WW', 'VIP', 'Showroom'
            $table->tinyInteger('status')->default(0); //0-uncleaned, 1-cleaned, 2-red card, 3-volunteer
            $table->unsignedBigInteger('volunteer')->nullable(); //volunteer
            $table->unsignedBigInteger('record_id')->nullable(); //record
            
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('cascade');
            $table->primary(['calendar_id', 'room_id']);
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
        // Schema::table('calendar_room', function (Blueprint $table) {
        //     //
        // });
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('calendar_room');
        Schema::enableForeignKeyConstraints();
        // Schema::drop('calendar_room');
    }
}
