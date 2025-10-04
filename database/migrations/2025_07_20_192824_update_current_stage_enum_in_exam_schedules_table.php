<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to modify the ENUM column to add the new value
        DB::statement("ALTER TABLE exam_schedules MODIFY COLUMN current_stage ENUM('faculty', 'exam_cell', 'tc_admin', 'aa', 'completed') DEFAULT 'faculty'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'completed' from the ENUM
        DB::statement("ALTER TABLE exam_schedules MODIFY COLUMN current_stage ENUM('faculty', 'exam_cell', 'tc_admin', 'aa') DEFAULT 'faculty'");
    }
};
