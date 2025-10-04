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
        Schema::create('all_tc_lms', function (Blueprint $table) {
            $table->id();
            $table->string('tc_code')->index(); // TC unique code
            $table->string('faculty_code')->index(); // Faculty unique code from users table
            $table->string('site_url')->unique(); // Unique site URL
            $table->string('site_department'); // Department name (added by admin)
            $table->longText('site_contents')->nullable(); // Drag and drop content (HTML/CSS/JS)
            $table->boolean('is_approved')->default(false); // Approval status
            $table->unsignedBigInteger('approved_by')->nullable(); // Admin who approved
            $table->timestamp('approved_at')->nullable(); // Approval timestamp
            $table->string('site_title')->nullable(); // Site title
            $table->text('site_description')->nullable(); // Site description
            $table->string('status')->default('draft'); // draft, submitted, approved, rejected
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('tc_code')->references('tc_code')->on('tc_shot_code')->onDelete('cascade');
            $table->foreign('faculty_code')->references('email')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index(['tc_code', 'site_department']);
            $table->index(['is_approved', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_tc_lms');
    }
};
