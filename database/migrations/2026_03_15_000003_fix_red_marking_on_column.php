<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->time('red_marking_on')->nullable()->change();
            $table->string('meal_option', 255)->nullable()->change();
            $table->string('dinner_count_option', 255)->nullable()->change();
            $table->string('status', 50)->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert this fix
    }
};
