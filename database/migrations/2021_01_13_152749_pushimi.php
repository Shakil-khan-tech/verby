<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Pushimi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pushimi', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('employee_id');
          $table->integer('old_id')->nullable();
          $table->date('data');
          $table->date('fillimi');
          $table->date('mbarimi');
          $table->timestamps();

          $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
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
        Schema::dropIfExists('pushimi');
        Schema::enableForeignKeyConstraints();
    }
}
