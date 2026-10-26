<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Roles: `permission` holds the JSON module/action map, `color` the badge color on the users list.
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->longText('permission')->nullable();
            $table->string('status', 10)->nullable();
            $table->string('color', 20)->nullable();
            $table->integer('addedby_id')->nullable();
            $table->integer('editedby_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
