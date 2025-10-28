<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Créer une nouvelle table temporaire
        Schema::create('appointments_temp', function (Blueprint $table) {
            $table->id();
            // Copier toutes les colonnes de la table originale...
            $table->enum('status', ['pending', 'confirmed', 'canceled', 'rescheduled', 'completed'])->default('pending');
            // ... autres colonnes
            $table->timestamps();
        });

        // Copier les données
        DB::statement('INSERT INTO appointments_temp SELECT * FROM appointments');

        // Supprimer l'ancienne table
        Schema::drop('appointments');

        // Renommer la nouvelle table
        Schema::rename('appointments_temp', 'appointments');
    }

    public function down(): void
    {
        // Inverser le processus si nécessaire
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
