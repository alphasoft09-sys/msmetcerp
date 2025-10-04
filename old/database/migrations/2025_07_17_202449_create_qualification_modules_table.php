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
        Schema::create('qualification_modules', function (Blueprint $table) {
            $table->id();
            $table->string('module_name');
            $table->string('nos_code');
            $table->boolean('is_optional')->default(false);
            $table->integer('hour');
            $table->decimal('credit', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualification_modules');
    }
};
