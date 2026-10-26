<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->index();
            $table->string('event')->index();
            $table->string('title')->nullable();
            $table->string('user_type')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('loggable_type')->nullable()->index();
            $table->unsignedBigInteger('loggable_id')->nullable()->index();
            $table->json('data')->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->string('url', 1024)->nullable();
            $table->string('method', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
