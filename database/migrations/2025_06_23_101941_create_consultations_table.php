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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->date('date_prescription')->nullable();
            $table->string('type')->nullable();
            $table->string('motif')->nullable();
            $table->text('diagnostic')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'canceled'])->nullable();
            $table->text('traitement')->nullable();
            $table->text('notes')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
