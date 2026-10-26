<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Login accounts only — employee data lives in the HR package (hr_employees).
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('email', 100)->nullable()->index();
            $table->string('mobile', 100)->nullable()->index();
            $table->string('employee_id', 100)->nullable();
            $table->string('signature')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('admin')->default(0);
            $table->boolean('staff')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('password_show', 191)->nullable();
            $table->rememberToken();
            $table->bigInteger('addedby_id')->nullable();
            $table->timestamp('addedby_at')->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
