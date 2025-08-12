<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'married_status')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('married_status')->nullable()->after('maried');
            });
        }
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'married_since')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->date('married_since')->nullable()->after('additional_income');
            });
        }
    }

    public function down(): void
    {
    }
};
