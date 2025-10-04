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
            $table->string('from_tc')->nullable()->after('email');
            $table->integer('user_role')->default(1)->after('from_tc'); // 1=TC Admin, 2=TC Head, 3=TC Exam Cell, 4=TC AA, 5=TC Faculty
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['from_tc', 'user_role']);
        });
    }
};
