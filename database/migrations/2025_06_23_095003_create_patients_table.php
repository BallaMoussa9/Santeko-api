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
            //$table->unsignedBigInteger('bed_id')->nullable();
            $table->date('last_consultation_date')->nullable();

            // Pour l'ENUM 'status', vous pouvez le rendre nullable, mais conservez la définition de l'ENUM
            // ATTENTION: Si la colonne a déjà une valeur par défaut ('actif'), la rendre nullable peut nécessiter de l'enlever.
            // Le plus simple est de le marquer comme nullable et de s'assurer qu'il n'y a pas de NOT NULL dans la DB.
            $table->enum('status', ['actif', 'en_traitement', 'stable', 'critique', 'sorti', 'archive'])->nullable();
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
