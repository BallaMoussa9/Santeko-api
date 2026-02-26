<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ÉTAPE 1 : Couper les liens dans blood_units d'abord
        Schema::table('blood_units', function (Blueprint $table) {
            // On désactive la contrainte de clé étrangère sur donor_id
            if (Schema::hasColumn('blood_units', 'donor_id')) {
                // Sur MySQL, il faut souvent supprimer la clé étrangère AVANT la colonne
                $table->dropForeign(['donor_id']); 
                $table->dropColumn('donor_id');
            }

            // ÉTAPE 2 : Ajouter le nouveau lien vers les patients
            $table->foreignId('patient_id')
                  ->after('id')
                  ->constrained('patients')
                  ->onDelete('cascade');
        });

        // ÉTAPE 3 : Maintenant on peut supprimer la table donors sans erreur
        // On désactive temporairement les vérifications pour être sûr
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('donors');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_units', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropColumn('patient_id');
            $table->unsignedBigInteger('donor_id')->nullable()->after('id');
        });
    }
};