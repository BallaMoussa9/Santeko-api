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
        Schema::create('labtechnicians', function (Blueprint $table) {
            $table->id();
            $table->string('speciality')->nullable();
            $table->string('qualification')->nullable();
            $table->string('status')->default('active')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labtechnicians');
    }
};
