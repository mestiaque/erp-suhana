<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_sheets', function (Blueprint $table) {
            // Additional earning fields
            $table->decimal('special_overtime_amount', 10, 2)->default(0)->after('overtime_amount');
            $table->decimal('grass_time_amount', 10, 2)->default(0)->after('bonus');
            $table->decimal('other_bonus', 10, 2)->default(0)->after('grass_time_amount');
            
            // Additional deduction fields
            $table->decimal('salary_advance_deduction', 10, 2)->default(0)->after('loan_deduction');
            $table->decimal('deduction', 10, 2)->default(0)->after('salary_advance_deduction');
        });
    }

    public function down(): void
    {
        Schema::table('salary_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'special_overtime_amount',
                'grass_time_amount', 
                'other_bonus',
                'salary_advance_deduction',
                'deduction'
            ]);
        });
    }
};
