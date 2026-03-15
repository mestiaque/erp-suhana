<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_increments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('increment_date');
            $table->decimal('previous_salary', 10, 2)->default(0);
            $table->decimal('increment_amount', 10, 2)->default(0);
            $table->decimal('increment_percentage', 5, 2)->default(0);
            $table->decimal('new_salary', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_increments');
    }
};
