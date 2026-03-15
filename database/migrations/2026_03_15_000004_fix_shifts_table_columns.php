<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix red_marking_on column (boolean to time)
        DB::statement("ALTER TABLE shifts MODIFY red_marking_on TIME NULL");
        
        // Fix meal_option column (int to varchar)
        DB::statement("ALTER TABLE shifts MODIFY meal_option VARCHAR(255) NULL");
        
        // Fix dinner_count_option column (int to varchar)
        DB::statement("ALTER TABLE shifts MODIFY dinner_count_option VARCHAR(255) NULL");
        
        // Fix status column (int to varchar)
        DB::statement("ALTER TABLE shifts MODIFY status VARCHAR(50) DEFAULT 'active'");
        
        // Fix weekly overtime columns (boolean to time)
        DB::statement("ALTER TABLE shifts MODIFY weekly_overtime_allowed TIME NULL");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_sat TIME NULL");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_sun TIME NULL");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_mon TIME NULL");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_tue TIME NULL");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_wed TIME NULL");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_thu TIME NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE shifts MODIFY red_marking_on TINYINT(1) DEFAULT 0");
        DB::statement("ALTER TABLE shifts MODIFY meal_option INT DEFAULT 0");
        DB::statement("ALTER TABLE shifts MODIFY dinner_count_option INT DEFAULT 0");
        DB::statement("ALTER TABLE shifts MODIFY status INT DEFAULT 1");
        DB::statement("ALTER TABLE shifts MODIFY weekly_overtime_allowed TINYINT(1) DEFAULT 0");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_sat TINYINT(1) DEFAULT 0");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_sun TINYINT(1) DEFAULT 0");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_mon TINYINT(1) DEFAULT 0");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_tue TINYINT(1) DEFAULT 0");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_wed TINYINT(1) DEFAULT 0");
        DB::statement("ALTER TABLE shifts MODIFY weekly_ot_thu TINYINT(1) DEFAULT 0");
    }
};
