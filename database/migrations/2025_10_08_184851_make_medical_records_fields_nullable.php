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
        // Utiliser Schema::table pour modifier la table existante
        Schema::table('medicalrecords', function (Blueprint $table) {

            // Rendre nullables les clés étrangères (unsignedBigInteger)
            // Note: Nous utilisons le type exact de la olonne pour le 'change()'
            $table->unsignedBigInteger('patient_id')->nullable()->change();
            $table->unsignedBigInteger('doctor_id')->nullable()->change();
            $table->unsignedBigInteger('hospital_id')->nullable()->change();

            // Rendre nullables les autres champs
            $table->text('chronic_conditions')->nullable()->change();
            $table->string('numero_dossier')->nullable();

            // Rendre nullable l'ENUM 'status'.
            // ATTENTION: Nous supprimons la valeur par défaut '[active]'
            // car le statut pourrait être NULL au lieu d'une valeur de la liste.
            $table->enum('status', ['active', 'archived', 'private'])->nullable()->change();

            // Note : 'id', 'created_at', et 'updated_at' sont conservés tels quels (non modifiés)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // C'est la fonction de rollback. Pour inverser, on enlèverait l'état nullable,
        // mais cela peut causer des erreurs si des valeurs NULL existent en base.
        // On laisse souvent la fonction down vide ou on remet l'état initial (avec prudence).
        Schema::table('medical_records', function (Blueprint $table) {
            // Exemple : $table->unsignedBigInteger('patient_id')->nullable(false)->change();
        });
    }
};
