<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('hr_salary_keys', function (Blueprint $table) {
            $table->string('salary_approved_person_1', 191)->nullable()->change();
            $table->string('salary_approved_person_2', 191)->nullable()->change();
            $table->string('salary_approved_person_3', 191)->nullable()->change();
            $table->string('salary_approved_person_4', 191)->nullable()->change();
            $table->string('salary_approved_person_5', 191)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('hr_salary_keys', function (Blueprint $table) {
            $table->integer('salary_approved_person_1')->nullable()->change();
            $table->integer('salary_approved_person_2')->nullable()->change();
            $table->integer('salary_approved_person_3')->nullable()->change();
            $table->integer('salary_approved_person_4')->nullable()->change();
            $table->integer('salary_approved_person_5')->nullable()->change();
        });
    }
};
