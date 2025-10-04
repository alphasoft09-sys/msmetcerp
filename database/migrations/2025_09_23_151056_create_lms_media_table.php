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
        Schema::create('lms_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lms_site_id'); // Foreign key to all_tc_lms
            $table->string('media_type'); // image, video, document
            $table->string('original_name'); // Original filename
            $table->string('file_name'); // Stored filename
            $table->string('file_path'); // Storage path
            $table->string('file_url'); // Public URL
            $table->string('mime_type'); // File MIME type
            $table->unsignedBigInteger('file_size'); // File size in bytes
            $table->string('alt_text')->nullable(); // Alt text for images
            $table->text('description')->nullable(); // Media description
            $table->json('metadata')->nullable(); // Additional metadata (dimensions, etc.)
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('lms_site_id')->references('id')->on('all_tc_lms')->onDelete('cascade');
            
            // Indexes
            $table->index(['lms_site_id', 'media_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_media');
    }
};