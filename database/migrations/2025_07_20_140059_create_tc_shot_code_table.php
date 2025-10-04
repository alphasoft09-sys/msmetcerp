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
        Schema::create('tc_shot_code', function (Blueprint $table) {
            $table->id();
            $table->string('tc_code')->unique();
            $table->string('shot_code', 2)->unique();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('tc_code');
            $table->index('shot_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tc_shot_code');
    }
};
