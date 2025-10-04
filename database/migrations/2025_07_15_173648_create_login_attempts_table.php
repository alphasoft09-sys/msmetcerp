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
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->enum('guard', ['web', 'student'])->default('web');
            $table->boolean('success')->default(false);
            $table->text('attempted_password')->nullable();
            $table->timestamp('attempted_at');
            $table->boolean('alert_sent')->default(false);
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['email', 'ip_address']);
            $table->index(['email', 'ip_address', 'success']);
            $table->index('attempted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
