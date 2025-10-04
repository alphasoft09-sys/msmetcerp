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
            // Add TOA (Training of Assessor) fields
            $table->boolean('toa_done')->default(false)->after('sme_module_ids');
            $table->date('toa_certification_date')->nullable()->after('toa_done');
            $table->string('toa_certificate_number')->nullable()->after('toa_certification_date');
            $table->timestamp('toa_completed_at')->nullable()->after('toa_certificate_number');
            $table->string('toa_version')->nullable()->after('toa_completed_at');
            $table->text('toa_notes')->nullable()->after('toa_version');
            
            // Add unique indexes to prevent duplicate certificate numbers
            $table->unique(['toa_certificate_number'], 'unique_toa_certificate_number');
            $table->unique(['tot_certificate_number'], 'unique_tot_certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_profiles', function (Blueprint $table) {
            // Drop unique constraints first
            $table->dropUnique('unique_toa_certificate_number');
            $table->dropUnique('unique_tot_certificate_number');
            
            // Drop TOA fields
            $table->dropColumn([
                'toa_done',
                'toa_certification_date', 
                'toa_certificate_number',
                'toa_completed_at',
                'toa_version',
                'toa_notes'
            ]);
        });
    }
};
