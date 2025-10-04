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
        Schema::table('lms_departments', function (Blueprint $table) {
            if (!Schema::hasColumn('lms_departments', 'department_slug')) {
                $table->string('department_slug')->nullable()->after('department_name');
            }
        });

        // Generate slugs for existing departments
        $departments = \App\Models\LmsDepartment::all();
        foreach ($departments as $department) {
            if (empty($department->department_slug)) {
                $department->department_slug = \Str::slug($department->department_name);
                $department->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_departments', function (Blueprint $table) {
            $table->dropColumn('department_slug');
        });
    }
};