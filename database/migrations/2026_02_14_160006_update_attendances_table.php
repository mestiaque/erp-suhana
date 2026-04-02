<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('attendances', 'date')) {
                $table->date('date')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'in_time')) {
                $table->time('in_time')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'out_time')) {
                $table->time('out_time')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'work_hour')) {
                $table->decimal('work_hour', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('attendances', 'late_time')) {
                $table->integer('late_time')->default(0)->comment('in minutes');
            }
            if (!Schema::hasColumn('attendances', 'early_out')) {
                $table->integer('early_out')->default(0)->comment('in minutes');
            }
            if (!Schema::hasColumn('attendances', 'overtime')) {
                $table->decimal('overtime', 5, 2)->default(0)->comment('in hours');
            }
            if (!Schema::hasColumn('attendances', 'status')) {
                $table->enum('status', ['present', 'absent', 'late', 'leave', 'weekly_off', 'holiday', 'tour'])->default('present');
            }
            if (!Schema::hasColumn('attendances', 'location_lat')) {
                $table->string('location_lat')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'location_long')) {
                $table->string('location_long')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'remarks')) {
                $table->text('remarks')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // We don't drop columns in case they're used elsewhere
        });
    }
};
