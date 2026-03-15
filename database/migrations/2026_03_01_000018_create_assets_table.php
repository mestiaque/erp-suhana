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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // laptop, phone, vehicle, equipment
            $table->string('serial_number')->nullable();
            $table->string('model')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('value', 12, 2)->default(0);
            $table->enum('status', ['available', 'assigned', 'maintenance', 'retired'])->default('available');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_distributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('user_id');
            $table->date('assignment_date');
            $table->date('return_date')->nullable();
            $table->text('condition_on_assign')->nullable();
            $table->text('condition_on_return')->nullable();
            $table->enum('status', ['active', 'returned'])->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_distributions');
        Schema::dropIfExists('assets');
    }
};
