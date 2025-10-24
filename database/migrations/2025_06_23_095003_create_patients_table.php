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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('genre')->nullable();
            $table->string('group-sanguine')->nullable();
            $table->string('telephone_urgence')->nullable();
            $table->string('maladies_chroniques')->nullable();
            $table->string('assurance_maladie')->nullable();
            $table->string('numero_urgence')->nullable();
            $table->float('poids')->nullable();
            $table->float('taille')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
