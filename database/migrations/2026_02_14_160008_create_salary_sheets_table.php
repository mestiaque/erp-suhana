<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('house_rent', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('other_allowance', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->decimal('overtime_amount', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('total_earning', 10, 2)->default(0);
            $table->decimal('absent_deduction', 10, 2)->default(0);
            $table->decimal('late_deduction', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('provident_fund', 10, 2)->default(0);
            $table->decimal('loan_deduction', 10, 2)->default(0);
            $table->decimal('other_deduction', 10, 2)->default(0);
            $table->decimal('total_deduction', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->integer('working_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->enum('payment_method', ['bank', 'cash', 'mobile_banking'])->default('bank');
            $table->enum('payment_status', ['pending', 'paid', 'held'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->enum('salary_type', ['regular', 'new_employee', 'retired_employee'])->default('regular');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_sheets');
    }
};
