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
        Schema::table('qualification_module_mappings', function (Blueprint $table) {
            // Drop the existing foreign key constraint that references the non-existent modules table
            try {
                $table->dropForeign(['module_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            // Add the correct foreign key constraint to qualification_modules table
            $table->foreign('module_id')->references('id')->on('qualification_modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qualification_module_mappings', function (Blueprint $table) {
            // Drop the correct foreign key constraint
            try {
                $table->dropForeign(['module_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        });
    }
};
