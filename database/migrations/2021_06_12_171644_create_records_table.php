<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
          $table->id();
          // $table->string('device')->nullable();
          $table->unsignedBigInteger('old_id')->nullable();
          $table->unsignedBigInteger('employee_id');
          $table->unsignedBigInteger('device_id');
          // $table->integer('userid')->nullable();
          // $table->integer('mode')->nullable();
          $table->tinyInteger('action')->default(0); //checkin, checkout, pausein, pauseout
          $table->tinyInteger('perform')->default(0); // stewarding, unterhalt, room control, zimmer reinigung
          // $table->text('rooms')->nullable(); //a comma separated list of rooms cleaned
          $table->tinyInteger('identity')->default(0); //card, pin, camera
          $table->datetime('time');
          $table->unsignedBigInteger('calendar_id')->nullable();
          $table->unsignedBigInteger('user_id')->nullable();
          // $table->datetime('timestamp');
          $table->timestamps();

          $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
          $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
          $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('set null');
          $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('records');
        Schema::enableForeignKeyConstraints();
    }
}
