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
        Schema::table('vital_signs', function (Blueprint $table) {
            // 1. SUPPRIMER l'ancienne clé étrangère (pointant vers 'users')
            // Le nom par défaut est généralement 'nom_table_nom_colonne_foreign'
            $table->dropForeign('vital_signs_nurse_id_foreign');
        });

        Schema::table('vital_signs', function (Blueprint $table) {
            // 2. AJOUTER la nouvelle clé étrangère (pointant vers 'nurses')
            $table->foreign('nurse_id')
                  ->references('id')
                  ->on('nurses') // 🔑 C'est la table cible correcte
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pour annuler, on fait l'inverse :
        Schema::table('vital_signs', function (Blueprint $table) {
            // Supprimer la clé étrangère pointant vers 'nurses'
            $table->dropForeign('vital_signs_nurse_id_foreign');

            // Remettre l'ancienne clé étrangère pointant vers 'users' (si nécessaire, ou omettre)
            // L'omettre est souvent plus sûr si l'ancienne n'était pas fonctionnelle.
            // Si vous devez la remettre pour des raisons historiques/testing, elle serait :
            // $table->foreign('nurse_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
