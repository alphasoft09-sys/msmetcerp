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
            // Change site_contents from longText to longText (MySQL LONGTEXT can store up to 4GB)
            $table->longText('site_contents')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_tc_lms', function (Blueprint $table) {
            // Revert back to longText (they're the same in Laravel)
            $table->longText('site_contents')->nullable()->change();
        });
    }
};