<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LohnabrechnungRevision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('lohnabrechnung_revisions', function (Blueprint $table) {
        $table->id();
        // $table->integer('userID');
        $table->integer('old_id')->nullable();
        $table->unsignedBigInteger('employee_id');
        $table->datetime('date');
        $table->integer('konfirm')->default(0);
        $table->string('rroga')->default('0');
        $table->string('oret');
        $table->string('ehchf')->nullable();
        $table->string('BVG')->nullable();
        $table->string('KONTO_Ferie')->nullable();
        $table->string('KONTO_Ferie_PAY')->nullable();
        $table->string('KONTO_13monats')->nullable();
        $table->string('KONTO_13monats_PAY')->nullable();
        $table->integer('B_KTG_1')->nullable();
        $table->integer('B_KTG_2')->nullable();
        $table->integer('B_unfall_1')->nullable();
        $table->integer('B_unfall_2')->nullable();
        $table->integer('decki250')->nullable();
        $table->integer('decki200')->nullable();
        $table->string('AHV')->nullable();
        $table->string('ALV')->nullable();
        $table->string('NBUV')->nullable();
        $table->string('B_bonnus1_1')->nullable();
        $table->integer('B_bonnus1_2')->nullable();
        $table->string('B_bonnus2_1')->nullable();
        $table->integer('B_bonnus2_2')->nullable();
        $table->integer('A_Verplegung_1')->nullable();
        $table->integer('A_Verplegung_2')->nullable();
        $table->string('A_bonnus1_1')->nullable();
        $table->integer('A_bonnus1_2')->nullable();
        $table->string('A_bonnus2_1')->nullable();
        $table->integer('A_bonnus2_2')->nullable();
        $table->datetime('timestamp')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

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
        Schema::dropIfExists('lohnabrechnung_revisions');
        Schema::enableForeignKeyConstraints();
    }
}
