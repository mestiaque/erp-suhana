<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generals', function (Blueprint $table) {
            $table->json('hidden_permission_modules')->nullable()->after('signature');
        });
    }

    public function down(): void
    {
        Schema::table('generals', function (Blueprint $table) {
            $table->dropColumn('hidden_permission_modules');
        });
    }
};
