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
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('faculty_id');
            $table->string('tc_code');
            $table->string('assessment_type'); // quiz, test, assignment, exam, etc.
            $table->string('title');
            $table->decimal('score', 5, 2)->nullable(); // Score out of 100
            $table->decimal('max_score', 5, 2)->default(100); // Maximum possible score
            $table->date('assessment_date');
            $table->text('comments')->nullable();
            $table->string('grade')->nullable(); // A, B, C, D, F
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('student_logins')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('faculty_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['tc_code', 'student_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_progress');
    }
};
