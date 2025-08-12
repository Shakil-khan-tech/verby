<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Plani extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plani', function (Blueprint $table) {
          $table->id();
          // $table->integer('userid');
          $table->unsignedBigInteger('employee_id');
          $table->integer('old_id')->nullable();
          // $table->string('device');
          $table->unsignedBigInteger('device_id');
          $table->date('dita');
          $table->string('symbol');
          // $table->datetime('timestamp');
          $table->timestamps();

          $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
          $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');

          $table->unique(['employee_id', 'device_id', 'dita']);
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
        Schema::dropIfExists('plani');
        Schema::enableForeignKeyConstraints();
    }
}
