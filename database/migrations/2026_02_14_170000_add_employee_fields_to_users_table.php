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
        Schema::table('users', function (Blueprint $table) {
            // Salary Breakdown Fields (if not exist)
            if (!Schema::hasColumn('users', 'basic_salary')) {
                $table->decimal('basic_salary', 10, 2)->nullable()->after('salary_amount');
            }
            if (!Schema::hasColumn('users', 'house_rent')) {
                $table->decimal('house_rent', 10, 2)->nullable()->after('basic_salary');
            }
            if (!Schema::hasColumn('users', 'medical_allowance')) {
                $table->decimal('medical_allowance', 10, 2)->nullable()->after('house_rent');
            }
            if (!Schema::hasColumn('users', 'transport_allowance')) {
                $table->decimal('transport_allowance', 10, 2)->nullable()->after('medical_allowance');
            }
            if (!Schema::hasColumn('users', 'food_allowance')) {
                $table->decimal('food_allowance', 10, 2)->nullable()->after('transport_allowance');
            }
            if (!Schema::hasColumn('users', 'conveyance_allowance')) {
                $table->decimal('conveyance_allowance', 10, 2)->nullable()->after('food_allowance');
            }
            if (!Schema::hasColumn('users', 'provident_fund')) {
                $table->decimal('provident_fund', 10, 2)->nullable()->after('conveyance_allowance');
            }

            // Employment Dates (if not exist)
            if (!Schema::hasColumn('users', 'joining_date')) {
                $table->date('joining_date')->nullable()->after('created_at');
            }
            if (!Schema::hasColumn('users', 'confirmation_date')) {
                $table->date('confirmation_date')->nullable()->after('joining_date');
            }
            if (!Schema::hasColumn('users', 'retirement_date')) {
                $table->date('retirement_date')->nullable()->after('confirmation_date');
            }

            // Photo and Signature paths (if not exist)
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('profile');
            }
            if (!Schema::hasColumn('users', 'signature')) {
                $table->string('signature')->nullable()->after('photo');
            }

            // Employee Status (if not exist)
            if (!Schema::hasColumn('users', 'employee_status')) {
                $table->enum('employee_status', ['active', 'inactive', 'retired'])->default('active')->after('employment_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'basic_salary', 'house_rent', 'medical_allowance', 'transport_allowance',
                'food_allowance', 'conveyance_allowance', 'provident_fund',
                'joining_date', 'confirmation_date', 'retirement_date',
                'photo', 'signature', 'employee_status'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
