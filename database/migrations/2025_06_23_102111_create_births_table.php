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
        Schema::create('births', function (Blueprint $table) {

            $table->id();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('sexe')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('father_name')->nullable();
            $table->float('poids')->nullable();
            $table->float('taille')->nullable();
            $table->time('heure_naissance')->nullable();
            $table->enum('statut', ['pending', 'confirmed', 'canceled'])->nullable();
            $table->string('numero_acte_naissance')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('births');
    }
};
