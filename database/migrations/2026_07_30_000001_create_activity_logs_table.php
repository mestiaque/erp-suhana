<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activity_logs')) {
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
                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('activity_logs', 'uuid')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->index()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('activity_logs') && Schema::hasColumn('activity_logs', 'uuid')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};
