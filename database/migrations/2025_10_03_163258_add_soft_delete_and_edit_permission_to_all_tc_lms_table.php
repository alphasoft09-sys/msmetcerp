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
            $table->softDeletes(); // Adds deleted_at column
            $table->boolean('can_edit_after_approval')->default(false); // Admin permission to edit after approval
            $table->unsignedBigInteger('rejected_by')->nullable(); // Admin who rejected
            $table->timestamp('rejected_at')->nullable(); // Rejection timestamp
            $table->text('rejection_reason')->nullable(); // Rejection reason
            
            // Foreign key for rejected_by
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_tc_lms', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropColumn([
                'deleted_at',
                'can_edit_after_approval',
                'rejected_by',
                'rejected_at',
                'rejection_reason'
            ]);
        });
    }
};
