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
        // Supprimer d'abord les contraintes étrangères si elles existent
        Schema::table($this->tableName, function (Blueprint $table) {
            // Vérifier et supprimer les contraintes étrangères
            $this->dropForeignKeyIfExists($this->tableName, 'admin_id');
            $this->dropForeignKeyIfExists($this->tableName, 'recever_id');

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
    }

    /**
     * Annule les migrations (Recréation des colonnes).
     */
    public function down(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            // Recréer toutes les colonnes
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('recever_id')->nullable();
            $table->string('title')->nullable();
            $table->string('priority')->nullable()->default('normal');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            // Gérer la colonne status selon le SGBD
            if (DB::getDriverName() === 'pgsql') {
                // PostgreSQL: utiliser VARCHAR avec contrainte CHECK
                $table->string('status')->default($this->defaultStatus);
            } else {
                // MySQL: utiliser enum
                $table->enum('status', $this->statusValues)->default($this->defaultStatus);
            }
        });

        // Ajouter la contrainte CHECK pour PostgreSQL APRÈS la création de la colonne
        if (DB::getDriverName() === 'pgsql') {
            $quotedValues = implode(', ', array_map(fn($v) => "'$v'", $this->statusValues));
            $constraintName = "{$this->tableName}_status_check";

            // Supprimer la contrainte si elle existe déjà
            DB::statement("ALTER TABLE {$this->tableName} DROP CONSTRAINT IF EXISTS {$constraintName}");
            // Ajouter la contrainte CHECK
            DB::statement("ALTER TABLE {$this->tableName} ADD CONSTRAINT {$constraintName} CHECK (status IN ({$quotedValues}))");
        }

        // Recréer les contraintes étrangères
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('recever_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Méthode utilitaire pour supprimer une contrainte étrangère si elle existe.
     */
    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        $schema = DB::getDoctrineSchemaManager();
        $tableDetails = $schema->listTableDetails($table);

        foreach ($tableDetails->getForeignKeys() as $foreignKey) {
            if (in_array($column, $foreignKey->getLocalColumns())) {
                Schema::table($table, function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign([$foreignKey->getName()]);
                });
                break;
            }
        }
    }
};
