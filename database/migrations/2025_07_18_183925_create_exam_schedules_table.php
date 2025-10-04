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
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('tc_code');
            $table->string('course_name');
            $table->string('batch_code');
            $table->string('semester');
            $table->enum('exam_type', ['Internal', 'Final', 'Special Final']);
            $table->string('exam_coordinator');
            $table->date('exam_start_date');
            $table->date('exam_end_date');
            $table->string('program_number');
            $table->enum('status', ['draft', 'submitted', 'exam_cell_approved', 'tc_admin_approved', 'received', 'rejected', 'hold'])->default('draft');
            $table->enum('current_stage', ['faculty', 'exam_cell', 'tc_admin', 'aa'])->default('faculty');
            $table->text('comment')->nullable();
            $table->string('course_completion_file')->nullable();
            $table->string('student_details_file')->nullable();
            $table->boolean('terms_accepted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
};
