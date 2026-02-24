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
        Schema::table('production_plannings', function (Blueprint $table) {
            $table->string('color_name')->nullable()->after('style_no');
            $table->integer('color_qty')->default(0)->after('color_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_plannings', function (Blueprint $table) {
            $table->dropColumn(['color_name', 'color_qty']);
        });
    }
};
