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
        Schema::table('patients', function (Blueprint $table) {
            // 1. last_consultation_date (Date de dernière consultation)
            $table->date('last_consultation_date')->nullable()->after('taille');

            // 2. status (Statut du patient : ENUM)
            // L'énumération garantit que seules ces valeurs sont acceptées.
            // J'ai mis 'actif' comme valeur par défaut et rendu la colonne non nullable.
            $table->enum('status', ['actif', 'en_traitement', 'stable', 'critique', 'sorti', 'archive'])
                  ->default('actif')
                  ->nullable(false)
                  ->after('last_consultation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Suppression des colonnes en cas de rollback de la migration
            $table->dropColumn('status');
            $table->dropColumn('last_consultation_date');
        });
    }
};
