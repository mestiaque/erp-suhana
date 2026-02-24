<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('production_plannings', function (Blueprint $table) {
            $table->text('planning_month')->nullable()->after('master_plan_id'); // format: YYYY-MM
        });
    }

    public function down()
    {
        Schema::table('production_plannings', function (Blueprint $table) {
            $table->dropColumn('planning_month');
        });
    }
};
