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
        Schema::dropIfExists('modules');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('module_name');
            $table->string('nos_code')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_theory')->default(true);
            $table->timestamps();
        });
    }
};
