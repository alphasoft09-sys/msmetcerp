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
        Schema::create('lms_departments', function (Blueprint $table) {
            $table->id();
            $table->string('department_name')->unique(); // Department name (unique)
            $table->string('department_slug')->unique(); // URL-friendly slug
            $table->text('description')->nullable(); // Department description
            $table->boolean('is_active')->default(true); // Active status
            $table->unsignedBigInteger('created_by'); // Admin who created it
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['is_active', 'department_slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_departments');
    }
};
