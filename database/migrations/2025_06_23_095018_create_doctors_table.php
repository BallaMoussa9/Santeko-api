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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('speciality')->nullable();
            $table->string('numero_ordre')->nullable();
            $table->text('biography')->nullable();
            $table->integer('experience')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->nullable();
            $table->string('numero_professionel')->unique()->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
