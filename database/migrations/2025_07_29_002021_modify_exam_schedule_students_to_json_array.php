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
        Schema::table('exam_schedule_students', function (Blueprint $table) {
            // Drop the existing columns
            $table->dropColumn(['student_roll_no', 'is_selected']);
            
            // Add new JSON column to store student roll numbers as array
            $table->json('student_roll_numbers')->after('exam_schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_schedule_students', function (Blueprint $table) {
            // Drop the JSON column
            $table->dropColumn('student_roll_numbers');
            
            // Restore the original columns
            $table->string('student_roll_no')->after('exam_schedule_id');
            $table->boolean('is_selected')->default(false)->after('student_roll_no');
        });
    }
};
