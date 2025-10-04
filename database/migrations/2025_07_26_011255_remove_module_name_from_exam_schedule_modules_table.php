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
        Schema::table('exam_schedule_modules', function (Blueprint $table) {
            $table->dropColumn('module_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_schedule_modules', function (Blueprint $table) {
            $table->string('module_name')->after('exam_schedule_id');
        });
    }
};
