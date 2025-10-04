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
        Schema::table('exam_schedules', function (Blueprint $table) {
            // Action tracking fields
            $table->foreignId('rejected_by')->nullable()->after('held_by')->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->after('rejected_by')->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable()->after('approved_by');
            $table->timestamp('approved_at')->nullable()->after('rejected_at');
            $table->timestamp('held_at')->nullable()->after('approved_at');
            
            // Add indexes for better performance
            $table->index(['rejected_by', 'status']);
            $table->index(['held_by', 'status']);
            $table->index(['approved_by', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->dropIndex(['rejected_by', 'status']);
            $table->dropIndex(['held_by', 'status']);
            $table->dropIndex(['approved_by', 'status']);
            
            $table->dropForeign(['rejected_by']);
            $table->dropForeign(['approved_by']);
            
            $table->dropColumn([
                'rejected_by',
                'approved_by',
                'rejected_at',
                'approved_at',
                'held_at'
            ]);
        });
    }
};
