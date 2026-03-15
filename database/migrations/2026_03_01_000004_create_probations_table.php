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
        Schema::create('probations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('probation_start_date');
            $table->date('probation_end_date');
            $table->integer('months')->default(0);
            $table->enum('status', ['active', 'completed', 'extended', 'terminated'])->default('active');
            $table->text('performance_notes')->nullable();
            $table->enum('confirmation_status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->text('confirmation_notes')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('probations');
    }
};
