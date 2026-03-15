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
        Schema::create('performances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->year('year');
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4', 'annual'])->default('annual');
            $table->decimal('rating', 3, 2)->default(0); // 0-5 rating
            $table->decimal('attendance_score', 5, 2)->default(0);
            $table->decimal('task_completion', 5, 2)->default(0);
            $table->decimal('teamwork', 5, 2)->default(0);
            $table->decimal('initiative', 5, 2)->default(0);
            $table->decimal('punctuality', 5, 2)->default(0);
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('comments')->nullable();
            $table->text('goals')->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performances');
    }
};
