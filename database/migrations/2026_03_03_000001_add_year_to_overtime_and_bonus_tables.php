<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add year and month fields to overtime table for better salary integration
     */
    public function up(): void
    {
        // Add year and month to overtimes table
        Schema::table('overtimes', function (Blueprint $table) {
            if (!Schema::hasColumn('overtimes', 'year')) {
                $table->integer('year')->nullable()->after('overtime_date');
            }
            if (!Schema::hasColumn('overtimes', 'month')) {
                $table->integer('month')->nullable()->after('year');
            }
        });

        // Add year field to bonuses table
        Schema::table('bonuses', function (Blueprint $table) {
            if (!Schema::hasColumn('bonuses', 'year')) {
                $table->integer('year')->nullable()->after('month');
            }
        });

        // Add year field to deductions table
        Schema::table('deductions', function (Blueprint $table) {
            if (!Schema::hasColumn('deductions', 'year')) {
                $table->integer('year')->nullable()->after('month');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->dropColumn(['year', 'month']);
        });

        Schema::table('bonuses', function (Blueprint $table) {
            $table->dropColumn('year');
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->dropColumn('year');
        });
    }
};
