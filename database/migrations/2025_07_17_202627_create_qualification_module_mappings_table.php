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
        Schema::create('qualification_module_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qualification_id')->constrained('qualifications')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('qualification_modules')->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate mappings
            $table->unique(['qualification_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualification_module_mappings');
    }
};
