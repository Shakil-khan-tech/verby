<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_employee', function (Blueprint $table) {
            $table->unsignedBigInteger('device_id')->unsigned()->index();
            $table->unsignedBigInteger('employee_id')->unsigned()->index();

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->primary(['device_id', 'employee_id']);
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
        Schema::dropIfExists('device_employee');
        Schema::enableForeignKeyConstraints();
    }
}
