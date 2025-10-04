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
        Schema::table('admin_profiles', function (Blueprint $table) {
            // Add new field for SME qualification IDs
            $table->json('sme_qualification_ids')->nullable()->after('sme_module_ids');
            
            // Drop the old sme_module_ids field
            $table->dropColumn('sme_module_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_profiles', function (Blueprint $table) {
            // Recreate the old field
            $table->json('sme_module_ids')->nullable()->after('proficient_module_ids');
            
            // Drop the new field
            $table->dropColumn('sme_qualification_ids');
        });
    }
};
