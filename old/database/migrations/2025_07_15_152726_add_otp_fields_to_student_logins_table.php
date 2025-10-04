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
        Schema::table('student_logins', function (Blueprint $table) {
            $table->string('user_otp', 6)->nullable()->after('roll_number');
            $table->timestamp('otp_expires_at')->nullable()->after('user_otp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_logins', function (Blueprint $table) {
            $table->dropColumn(['user_otp', 'otp_expires_at']);
        });
    }
};
