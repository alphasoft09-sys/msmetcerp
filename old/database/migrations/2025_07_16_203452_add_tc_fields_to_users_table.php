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
        Schema::table('users', function (Blueprint $table) {
            $table->string('tc_name')->nullable()->after('from_tc');
            $table->text('tc_address')->nullable()->after('tc_name');
            $table->string('tc_phone')->nullable()->after('tc_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tc_name', 'tc_address', 'tc_phone']);
        });
    }
};
