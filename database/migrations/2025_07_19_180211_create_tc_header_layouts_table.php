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
        Schema::create('tc_header_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('tc_id'); // The from_tc code from users table
            $table->string('header_layout_url'); // Path to the uploaded image
            $table->timestamps();
            
            // Add unique constraint to ensure one header layout per TC
            $table->unique('tc_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tc_header_layouts');
    }
};
