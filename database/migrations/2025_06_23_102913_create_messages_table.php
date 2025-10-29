<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $tableName = 'messages';
    private array $statusValues = ['sent', 'delivered', 'read'];
    private string $defaultStatus = 'sent';

    /**
     * Exécute les migrations (Suppression de colonnes).
     */
    public function up(): void
    {
        // *** 1. DÉSACTIVER LES CONTRÔLES DE CLÉS ÉTRANGÈRES (Spécifique à MySQL) ***
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table($this->tableName, function (Blueprint $table) {

            // Tenter de supprimer les clés étrangères (Laravel gère si elles existent ou non, mais DBAL est requis pour les vérifications avancées)
            // Avec FOREIGN_KEY_CHECKS=0, cela ne devrait pas bloquer.
            try {
                 $table->dropForeign(['admin_id']);
            } catch (\Exception $e) {} // Ignorer les erreurs si la FK n'existe pas

            try {
                 $table->dropForeign(['recever_id']);
            } catch (\Exception $e) {} // Ignorer les erreurs si la FK n'existe pas

            // Supprimer les colonnes si elles existent
            $columnsToDrop = [
                'admin_id',
                'recever_id',
                'status',
                'priority',
                'start_time',
                'end_time',
                'title'
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn($this->tableName, $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // *** 2. RÉACTIVER LES CONTRÔLES DE CLÉS ÉTRANGÈRES ***
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Annule les migrations (Recréation des colonnes).
     */
    public function down(): void
    {
        // *** 1. DÉSACTIVER LES CONTRÔLES DE CLÉS ÉTRANGÈRES (Spécifique à MySQL) ***
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table($this->tableName, function (Blueprint $table) {
            // Recréer toutes les colonnes
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('recever_id')->nullable();
            $table->string('title')->nullable();
            $table->string('priority')->nullable()->default('normal');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->enum('status', $this->statusValues)->default($this->defaultStatus);
        });

        // Recréer les contraintes étrangères
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('recever_id')->references('id')->on('users')->onDelete('set null');
        });

        // *** 2. RÉACTIVER LES CONTRÔLES DE CLÉS ÉTRANGÈRES ***
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
