<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('admin_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('profile_photo')->nullable();
            $table->string('signature')->nullable();
            $table->string('qualification')->nullable();
            $table->string('contact_no')->nullable();
            $table->date('dob')->nullable();
            $table->enum('category', ['GEN', 'SC', 'ST', 'OTHER'])->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('course_completed_from')->nullable();
            $table->date('date_of_completion')->nullable();
            $table->string('current_section')->nullable();
            $table->string('designation')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->text('address_permanent')->nullable();
            $table->text('address_correspondence')->nullable();
            $table->boolean('tot_done')->default(false);
            $table->date('tot_certification_date')->nullable();
            $table->string('tot_certificate_number')->nullable();
            $table->foreignId('qualification_id')->nullable()->constrained('qualifications')->onDelete('set null');
            $table->boolean('is_sme')->default(false);
            $table->json('proficient_module_ids')->nullable();
            $table->json('sme_module_ids')->nullable();
            $table->timestamps();
            
            // Ensure one profile per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_profiles');
    }
};
