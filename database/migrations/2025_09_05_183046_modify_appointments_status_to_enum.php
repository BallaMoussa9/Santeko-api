<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si la table existe
        if (!Schema::hasTable('allergies')) {
            return;
        }

        // 🔹 Étape 1 : supprimer les contraintes CHECK existantes (si elles existent)
        try {
            DB::statement('ALTER TABLE allergies DROP CONSTRAINT IF EXISTS allergies_status_check');
        } catch (\Throwable $e) {
            // On ignore si la contrainte n’existe pas
        }

        // 🔹 Étape 2 : modifier le type de colonne en VARCHAR
        Schema::table('allergies', function (Blueprint $table) {
            $table->string('status', 255)->nullable()->change();
        });

        // 🔹 Étape 3 : ajouter une contrainte ENUM manuellement (version PostgreSQL)
        DB::statement("ALTER TABLE allergies ADD CONSTRAINT allergies_status_check CHECK (status IN ('actif', 'inactif', 'résolu'))");

        // 🔹 Étape 4 : valeur par défaut
        DB::statement("ALTER TABLE allergies ALTER COLUMN status SET DEFAULT 'actif'");
    }

    public function down(): void
    {
        Schema::table('allergies', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
        });

        try {
            DB::statement('ALTER TABLE allergies DROP CONSTRAINT IF EXISTS allergies_status_check');
        } catch (\Throwable $e) {}
    }
};
