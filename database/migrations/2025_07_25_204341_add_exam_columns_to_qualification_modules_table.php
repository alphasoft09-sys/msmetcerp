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
        Schema::table('qualification_modules', function (Blueprint $table) {
            $table->boolean('is_viva')->default(false)->after('credit');
            $table->boolean('is_practical')->default(false)->after('is_viva');
            $table->boolean('is_theory')->default(true)->after('is_practical');
            $table->integer('full_mark')->nullable()->after('is_theory');
            $table->integer('pass_mark')->nullable()->after('full_mark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qualification_modules', function (Blueprint $table) {
            $table->dropColumn(['is_viva', 'is_practical', 'is_theory', 'full_mark', 'pass_mark']);
        });
    }
};
