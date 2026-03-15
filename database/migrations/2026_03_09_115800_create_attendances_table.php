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
        Schema::create('attendances', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();

            $table->integer('in_minutes')->nullable();
            $table->integer('overtime_minutes')->nullable();

            $table->decimal('latitude',10,7)->nullable();
            $table->decimal('longitude',10,7)->nullable();

            $table->string('status')->nullable();
            $table->string('via')->nullable();
            $table->string('verify_type')->nullable();
            $table->string('device_sn')->nullable();

            $table->date('date')->nullable();

            $table->integer('work_hour')->nullable();
            $table->integer('late_time')->nullable();
            $table->integer('early_out')->nullable();
            $table->integer('overtime')->nullable();

            $table->decimal('location_lat',10,7)->nullable();
            $table->decimal('location_long',10,7)->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};