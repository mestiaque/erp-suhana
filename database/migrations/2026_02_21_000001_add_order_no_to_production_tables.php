<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add order_no to cuttings table only if it doesn't exist
        if (!Schema::hasColumn('cuttings', 'order_no')) {
            Schema::table('cuttings', function (Blueprint $table) {
                $table->string('order_no')->nullable()->after('pi_no');
                $table->string('color_name')->nullable()->after('style_no');
            });
        }

        // Create finishings table
        if (!Schema::hasTable('finishings')) {
            Schema::create('finishings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pi_id')->nullable();
                $table->string('pi_no')->nullable();
                $table->string('order_no')->nullable();
                $table->string('style_no')->nullable();
                $table->string('color_name')->nullable();
                $table->integer('finishing_qty')->default(0);
                $table->date('finishing_date')->nullable();
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // Create irons table
        if (!Schema::hasTable('irons')) {
            Schema::create('irons', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pi_id')->nullable();
                $table->string('pi_no')->nullable();
                $table->string('order_no')->nullable();
                $table->string('style_no')->nullable();
                $table->string('color_name')->nullable();
                $table->integer('iron_qty')->default(0);
                $table->date('iron_date')->nullable();
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // Create polies table
        if (!Schema::hasTable('polies')) {
            Schema::create('polies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pi_id')->nullable();
                $table->string('pi_no')->nullable();
                $table->string('order_no')->nullable();
                $table->string('style_no')->nullable();
                $table->string('color_name')->nullable();
                $table->integer('poly_qty')->default(0);
                $table->date('poly_date')->nullable();
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('polies');
        Schema::dropIfExists('irons');
        Schema::dropIfExists('finishings');

        if (Schema::hasColumn('cuttings', 'order_no')) {
            Schema::table('cuttings', function (Blueprint $table) {
                $table->dropColumn('order_no');
            });
        }
    }
};
