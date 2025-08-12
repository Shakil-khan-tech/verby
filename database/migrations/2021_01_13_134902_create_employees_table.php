<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('old_id')->nullable();
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('country_id')->nullable();
            $table->text('plani')->nullable();
            $table->boolean('PartTime')->default(1);
            $table->tinyInteger('function')->default(0); //'Gouvernante', 'Raumpflegerinnen', 'Unterhalt', 'Stewarding'
            $table->boolean('noqnaSmena')->default(0); //0=No, 1=Yes
            $table->string('name');
            $table->string('surname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('DOB')->nullable();
            $table->boolean('maried')->default(0); //0=No, 1=Yes
            $table->string('strasse')->nullable();
            $table->string('PLZ')->nullable();
            $table->string('ORT1')->nullable();
            $table->string('ORT');
            $table->string('AHV')->nullable();
            $table->string('bankname')->nullable();
            $table->string('IBAN')->nullable();
            $table->string('TAX')->nullable();
            $table->string('rroga')->nullable();
            $table->string('EhChf');
            $table->integer('decki200')->default(0);
            $table->integer('decki250')->default(0);
            $table->integer('BVG');
            $table->datetime('start')->nullable();
            $table->datetime('end')->nullable();
            $table->string('Perqind1')->nullable();
            $table->string('Perqind2')->nullable();
            $table->string('Perqind3')->nullable();
            $table->string('oldSaldoF')->nullable();
            $table->string('oldSaldo13')->nullable();
            $table->string('pin')->nullable();
            $table->string('card')->nullable();
            $table->text('camera')->nullable();
            $table->string('sage_number')->nullable();
            $table->boolean('api_monitoring')->default(0); //0=No, 1=Yes
            $table->decimal('work_percetage', 5,2)->nullable();
            $table->boolean('insurance_6_1')->nullable();
            $table->decimal('insurance_6_2', 5,2)->nullable();
            $table->decimal('insurance_6_3', 5,2)->nullable();
            $table->decimal('insurance_6_4', 5,2)->nullable();
            $table->boolean('insurance_6_5')->nullable();
            $table->text('insurance_7_1')->nullable();
            $table->boolean('insurance_15_1')->nullable();
            $table->boolean('insurance_15_2')->nullable();
            $table->string('insurance_15_3')->nullable();
            $table->boolean('insurance_15_4')->nullable();
            $table->string('insurance_15_5')->nullable();
            $table->string('insurance_15_6')->nullable();
            $table->string('insurance_15_7')->nullable();
            $table->text('insurance_16_1')->nullable();
            $table->string('additional_income')->nullable();
            $table->date('married_since')->nullable();
            $table->string('religion')->nullable();
            $table->string('children')->nullable();
            $table->string('child_allowance')->nullable();
            $table->string('work_permit')->nullable();
            $table->date('work_permit_expiry')->nullable();
            $table->tinyInteger('employee_type')->default(0)->comment('0 for old employee, 1 for new employee');
            $table->timestamps();

            // $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
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
        Schema::dropIfExists('employees');
        Schema::enableForeignKeyConstraints();
    }
}
