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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name_of_shift');
            $table->string('name_of_shift_bn')->nullable();
            $table->time('shift_starting_time');
            $table->time('red_marking_on')->nullable();
            $table->time('shift_closing_time');
            $table->boolean('shift_closing_time_next_day')->default(false);
            $table->time('over_time_allowed_up_to')->nullable();
            $table->boolean('over_time_allowed_up_to_next_day')->default(false);
            $table->time('over_time_1_allowed_up_to')->nullable();
            $table->boolean('over_time_1_allowed_up_to_next_day')->default(false);
            $table->time('card_accept_from')->nullable();
            $table->time('card_accept_to')->nullable();
            $table->boolean('card_accept_to_next_day')->default(false);
            $table->string('meal_option', 255)->nullable();
            $table->decimal('tiffin_allowance', 10, 2)->nullable();
            $table->boolean('no_lunch_hour_holiday')->default(false);
            $table->boolean('dinner_allowance')->default(false);
            $table->string('dinner_count_option', 255)->nullable();
            $table->boolean('double_shift')->default(false);
            $table->boolean('weekly_overtime_allowed')->default(false);
            $table->boolean('weekly_ot_sat')->default(false);
            $table->boolean('weekly_ot_sun')->default(false);
            $table->boolean('weekly_ot_mon')->default(false);
            $table->boolean('weekly_ot_tue')->default(false);
            $table->boolean('weekly_ot_wed')->default(false);
            $table->boolean('weekly_ot_thu')->default(false);
            $table->string('status', 50)->default('active');
            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->foreign('addedby_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
