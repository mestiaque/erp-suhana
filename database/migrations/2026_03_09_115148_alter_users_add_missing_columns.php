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
            'bn_name' => 'string',
            'photo' => 'string',
            'signature' => 'string',
            'location' => 'string',
            'grade_lavel' => 'string',

            'section_id' => 'unsignedBigInteger',
            'shift_id' => 'unsignedBigInteger',
            'line_number' => 'string',
            'report_to' => 'unsignedBigInteger',

            'father_name' => 'string',
            'father_name_bn' => 'string',
            'mother_name' => 'string',
            'mother_name_bn' => 'string',

            'spouse_name' => 'string',
            'spouse_name_bn' => 'string',

            'boys' => 'integer',
            'girls' => 'integer',

            'blood_group' => 'string',
            'religion' => 'string',
            'education' => 'string',
            'work_type' => 'string',

            'birth_registration' => 'string',
            'passport_no' => 'string',
            'driving_license' => 'string',
            'etin' => 'string',

            'distinguished_mark' => 'string',
            'height' => 'string',
            'weight' => 'string',

            'home_district' => 'string',
            'nationality' => 'string',

            'emergency_mobile' => 'string',
            'emergency_relation' => 'string',

            'other_information' => 'text',

            'reference_1' => 'string',
            'reference_2' => 'string',

            'nominee' => 'string',
            'nominee_bn' => 'string',
            'nominee_relation' => 'string',
            'nominee_age' => 'integer',

            'present_address' => 'text',
            'present_address_bn' => 'text',
            'permanent_address' => 'text',
            'permanent_address_bn' => 'text',

            'employee_type' => 'string',
            'nid_number' => 'string',

            'gross_salary' => 'decimal',
            'house_rent' => 'decimal',
            'medical_allowance' => 'decimal',
            'transport_allowance' => 'decimal',
            'food_allowance' => 'decimal',
            'conveyance_allowance' => 'decimal',
            'provident_fund' => 'decimal',

            'engineer' => 'boolean',
            'super_admin' => 'boolean',

            'joining_date' => 'date',
            'confirmation_date' => 'date',
            'retirement_date' => 'date',
        ];

        Schema::table('users', function (Blueprint $table) use ($columns) {

            foreach ($columns as $name => $type) {

                if (!Schema::hasColumn('users', $name)) {

                    if ($type === 'decimal') {
                        $table->decimal($name, 10, 2)->nullable();
                    } elseif ($type === 'boolean') {
                        $table->boolean($name)->default(0);
                    } elseif ($type === 'unsignedBigInteger') {
                        $table->unsignedBigInteger($name)->nullable();
                    } else {
                        $table->$type($name)->nullable();
                    }

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
            'bn_name','photo','signature','location','grade_lavel',
            'section_id','shift_id','line_number','report_to',
            'father_name','father_name_bn','mother_name','mother_name_bn',
            'spouse_name','spouse_name_bn',
            'boys','girls',
            'blood_group','religion','education','work_type',
            'birth_registration','passport_no','driving_license','etin',
            'distinguished_mark','height','weight',
            'home_district','nationality',
            'emergency_mobile','emergency_relation',
            'other_information',
            'reference_1','reference_2',
            'nominee','nominee_bn','nominee_relation','nominee_age',
            'present_address','present_address_bn',
            'permanent_address','permanent_address_bn',
            'employee_type','nid_number',
            'gross_salary','house_rent','medical_allowance',
            'transport_allowance','food_allowance','conveyance_allowance',
            'provident_fund',
            'engineer','super_admin',
            'joining_date','confirmation_date','retirement_date'
        ];

        Schema::table('users', function (Blueprint $table) use ($columns) {

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }

        });
    }
};
