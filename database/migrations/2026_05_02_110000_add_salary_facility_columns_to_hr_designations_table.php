<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('hr_designations')) {
            return;
        }

        Schema::table('hr_designations', function (Blueprint $table) {
            if (!Schema::hasColumn('hr_designations', 'gross_salary')) {
                $table->decimal('gross_salary', 12, 2)->nullable()->after('holiday_allowance');
            }
            if (!Schema::hasColumn('hr_designations', 'car_fuel')) {
                $table->decimal('car_fuel', 12, 2)->nullable()->after('gross_salary');
            }
            if (!Schema::hasColumn('hr_designations', 'phone_internet')) {
                $table->decimal('phone_internet', 12, 2)->nullable()->after('car_fuel');
            }
            if (!Schema::hasColumn('hr_designations', 'extra_facility')) {
                $table->decimal('extra_facility', 12, 2)->nullable()->after('phone_internet');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('hr_designations')) {
            return;
        }

        Schema::table('hr_designations', function (Blueprint $table) {
            $dropColumns = [];
            foreach (['gross_salary', 'car_fuel', 'phone_internet', 'extra_facility'] as $column) {
                if (Schema::hasColumn('hr_designations', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
