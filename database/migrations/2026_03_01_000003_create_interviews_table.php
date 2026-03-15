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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('position');
            $table->foreignId('department_id')->nullable()->constrained('attributes');
            $table->date('interview_date');
            $table->time('interview_time');
            $table->string('venue')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'pending', 'selected', 'rejected', 'on_hold'])->default('scheduled');
            $table->enum('interview_type', ['written', 'oral', 'practical', 'final'])->default('written');
            $table->foreignId('interviewer_id')->nullable()->constrained('users');
            $table->decimal('written_marks', 5, 2)->nullable();
            $table->decimal('oral_marks', 5, 2)->nullable();
            $table->decimal('practical_marks', 5, 2)->nullable();
            $table->decimal('total_marks', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
