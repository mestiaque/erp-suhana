<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roasters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->date('roster_date');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->enum('day_type', ['working', 'weekly_off', 'holiday'])->default('working');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roasters');
    }
};
