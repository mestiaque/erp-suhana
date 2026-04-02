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
        $columns = [
            'present_village',
            'present_village_bn',
            'present_post_office',
            'present_post_office_bn',
            'present_upazila',
            'present_upazila_bn',
            'present_district',
            'present_district_bn',
            'permanent_village',
            'permanent_village_bn',
            'permanent_post_office',
            'permanent_post_office_bn',
            'permanent_upazila',
            'permanent_upazila_bn',
            'permanent_district',
            'permanent_district_bn',
            'distinguished_mark_bn',
        ];

        Schema::table('users', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (!Schema::hasColumn('users', $column)) {
                    $table->string($column)->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = [
            'present_village',
            'present_village_bn',
            'present_post_office',
            'present_post_office_bn',
            'present_upazila',
            'present_upazila_bn',
            'present_district',
            'present_district_bn',
            'permanent_village',
            'permanent_village_bn',
            'permanent_post_office',
            'permanent_post_office_bn',
            'permanent_upazila',
            'permanent_upazila_bn',
            'permanent_district',
            'permanent_district_bn',
            'distinguished_mark_bn',
        ];

        Schema::table('users', function (Blueprint $table) use ($columns) {
            $existing = array_filter($columns, fn ($column) => Schema::hasColumn('users', $column));

            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }
};
