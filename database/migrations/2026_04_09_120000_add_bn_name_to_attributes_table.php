<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('attributes', 'bn_name')) {
            Schema::table('attributes', function (Blueprint $table) {
                $table->string('bn_name')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attributes', 'bn_name')) {
            Schema::table('attributes', function (Blueprint $table) {
                $table->dropColumn('bn_name');
            });
        }
    }
};
