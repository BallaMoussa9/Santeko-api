<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Ajout de la colonne bed_id
            $table->foreignId('bed_id')
                  ->nullable() // Un patient n'est pas toujours dans un lit
                  ->unique()   // Un seul patient peut pointer vers ce lit (relation 1-to-1)
                  ->constrained('beds') // Clé étrangère vers la table 'beds'
                  ->onDelete('set null') // Si le lit est supprimé, le patient n'y est plus assigné
                  ->after('user_id'); // Place la colonne après 'user_id'

            // Optionnel mais recommandé : Supprimer l'ancienne colonne patient_id de la table beds
            // Si vous souhaitez baser la relation uniquement sur patients.bed_id
            // (Si vous laissez beds.patient_id, vous avez une double relation, ce qui est déconseillé.)
            if (Schema::hasColumn('beds', 'patient_id')) {
                 Schema::table('beds', function (Blueprint $table) {
                     $table->dropConstrainedForeignId('patient_id');
                 });
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            // 1. Suppression de la contrainte de clé étrangère
            $table->dropConstrainedForeignId('bed_id');

            // 2. Suppression de la colonne
            $table->dropColumn('bed_id');
        });

        // Optionnel : Re-créer l'ancienne colonne patient_id sur la table beds si vous annulez la migration
        // Nécessite de vérifier si la colonne existe déjà pour éviter une erreur lors de la remontée de la migration.
        // if (!Schema::hasColumn('beds', 'patient_id')) {
        //      Schema::table('beds', function (Blueprint $table) {
        //          $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('set null');
        //      });
        // }
    }
};
