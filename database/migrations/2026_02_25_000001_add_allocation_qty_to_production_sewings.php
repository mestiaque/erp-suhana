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
        Schema::table('production_sewings', function (Blueprint $table) {
            $table->integer('allocation_qty')->default(0)->after('working_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_sewings', function (Blueprint $table) {
            $table->dropColumn('allocation_qty');
        });
    }
};
