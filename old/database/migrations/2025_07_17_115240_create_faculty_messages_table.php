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
        Schema::create('faculty_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faculty_id');
            $table->unsignedBigInteger('student_id')->nullable(); // null for broadcast messages
            $table->string('tc_code');
            $table->string('subject');
            $table->text('message');
            $table->enum('message_type', ['individual', 'class', 'broadcast'])->default('individual');
            $table->string('target_class')->nullable(); // for class-wide messages
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->foreign('faculty_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('student_logins')->onDelete('cascade');
            $table->index(['tc_code', 'faculty_id', 'message_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_messages');
    }
};
