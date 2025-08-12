<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplyListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('supply_id');
            $table->boolean('done')->default(0); //0=Missing, 1=Fixed
            $table->unsignedBigInteger('user_requested');
            $table->unsignedBigInteger('user_fixed')->nullable();
            $table->datetime('date_requested');
            $table->datetime('date_fixed')->nullable();
            $table->text('comment')->nullable();

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('supply_id')->references('id')->on('supplies')->onDelete('cascade');
            $table->foreign('user_requested')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_fixed')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supply_listings');
    }
}
