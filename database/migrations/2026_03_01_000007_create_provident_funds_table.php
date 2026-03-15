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
        Schema::create('provident_funds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('employee_contribution', 12, 2)->default(0); // Employee monthly contribution
            $table->decimal('company_contribution', 12, 2)->default(0); // Company monthly contribution
            $table->decimal('total_amount', 12, 2)->default(0); // Total accumulated
            $table->decimal('interest_rate', 5, 2)->default(0); // Interest rate %
            $table->year('year');
            $table->enum('month', ['01','02','03','04','05','06','07','08','09','10','11','12']);
            $table->enum('status', ['active', 'withdrawn', 'settled'])->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provident_funds');
    }
};
