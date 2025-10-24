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
        Schema::table('patients', function (Blueprint $table) {

            // Rendre nullables les champs existants que vous avez listés
            $table->string('genre')->nullable()->change();
            // CORRECTION: Assurez-vous d'utiliser 'group_sanguine' (underscore)
            $table->string('group_sanguine')->nullable()->change();
            $table->string('telephone_urgence')->nullable()->change();
            $table->string('maladies_chroniques')->nullable()->change();
            $table->string('assurance_maladie')->nullable()->change();
            $table->string('numero_urgence')->nullable()->change();

            // Les champs poids et taille sont varchar(255) dans votre structure actuelle
            $table->string('poids')->nullable()->change();
            $table->string('taille')->nullable()->change();

            // Rendre nullables les autres champs ajoutés plus tard
            $table->unsignedBigInteger('medical_record_id')->nullable()->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('bed_id')->nullable()->change();
            $table->date('last_consultation_date')->nullable()->change();

            // Pour l'ENUM 'status', vous pouvez le rendre nullable, mais conservez la définition de l'ENUM
            // ATTENTION: Si la colonne a déjà une valeur par défaut ('actif'), la rendre nullable peut nécessiter de l'enlever.
            // Le plus simple est de le marquer comme nullable et de s'assurer qu'il n'y a pas de NOT NULL dans la DB.
            $table->enum('status', ['actif', 'en_traitement', 'stable', 'critique', 'sorti', 'archive'])->nullable()->change();

            // Si vous aviez des contraintes de clés étrangères (foreign keys), vous devriez les gérer ici.
            // Pour des colonnes comme user_id et medical_record_id, c'est mieux d'utiliser des méthodes dédiées:

            // Si vous avez des problèmes de contraintes:
            // $table->dropForeign(['user_id']);
            // $table->unsignedBigInteger('user_id')->nullable()->change();
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pour annuler, on retire l'état 'nullable' (NON RECOMMANDÉ si des NULL sont déjà en base)
        // Mais techniquement, pour un rollback propre, on remet l'état initial.
        Schema::table('patients', function (Blueprint $table) {

            // Exemple : rendre à nouveau non-nullable (si c'était le cas avant)
            // C'EST DANGEREUX SI VOUS AVEZ DES VALEURS NULL EN PRODUCTION !
            // $table->string('genre')->nullable(false)->change();

        });
    }
};
