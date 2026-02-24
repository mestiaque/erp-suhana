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
            $table->decimal('smb', 8, 2)->nullable()->after('capacity_hour')->comment('Standard Minute Band / SAM');
            $table->integer('operators')->nullable()->after('smb')->comment('Number of operators');
            $table->integer('helpers')->nullable()->after('operators')->comment('Number of helpers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_sewings', function (Blueprint $table) {
            $table->dropColumn(['smb', 'operators', 'helpers']);
        });
    }
};
