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
        Schema::table('all_tc_lms', function (Blueprint $table) {
            $table->string('seo_title', 60)->nullable()->after('site_description');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->text('seo_keywords')->nullable()->after('seo_description');
            $table->string('seo_slug')->nullable()->after('seo_keywords');
            $table->json('structured_data')->nullable()->after('seo_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_tc_lms', function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description', 
                'seo_keywords',
                'seo_slug',
                'structured_data'
            ]);
        });
    }
};