<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Rooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        // $table->integer('userid');
        $table->integer('old_id');
        $table->unsignedBigInteger('employee_id');
        // $table->string('device');
        $table->unsignedBigInteger('device_id');
        $table->date('dita');
        $table->string('depa');
        $table->string('restant');
        $table->timestamps();

        $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
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
        Schema::dropIfExists('rooms');
        Schema::enableForeignKeyConstraints();
    }
}
