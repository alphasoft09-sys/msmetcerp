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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('tc_code'); // Training Center code
            $table->unsignedBigInteger('faculty_id'); // Faculty who teaches this subject
            $table->string('class_level'); // Class 10, 11, 12, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('faculty_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['tc_code', 'class_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
