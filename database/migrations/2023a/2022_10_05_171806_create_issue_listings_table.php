<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssueListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue_listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('issue_id');
            $table->boolean('done')->default(0); //0=Broken, 1=Fixed
            $table->unsignedBigInteger('user_requested');
            $table->string('email_fixed')->nullable();
            $table->datetime('date_requested');
            $table->datetime('date_fixed')->nullable();
            $table->text('comment_requested')->nullable();
            $table->text('comment_fixed')->nullable();
            $table->smallInteger('priority')->default(0); //0=Low, 1=Medium, 2=High, 3=Maximum

            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('issue_id')->references('id')->on('issues')->onDelete('cascade');
            $table->foreign('user_requested')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('issue_listings');
    }
}
