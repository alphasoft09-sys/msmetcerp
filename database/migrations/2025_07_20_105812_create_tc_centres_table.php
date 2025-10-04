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
        Schema::create('tc_centres', function (Blueprint $table) {
            $table->id();
            $table->string('tc_code'); // From TC code (linked to user's from_tc)
            $table->string('centre_name');
            $table->text('address');
            $table->timestamps();
            
            // Add index for better performance
            $table->index('tc_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tc_centres');
    }
};
