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
        Schema::table('users', function (Blueprint $table) {

            // On s'assure que les colonnes n'existent pas déjà pour éviter les erreurs
            // C'est facultatif si vous êtes sûr qu'elles n'existent pas
            if (!Schema::hasColumn('users', 'nurse_id')) {
                // 1. Ajout de la colonne nurse_id (clé étrangère)
                $table->foreignId('nurse_id')->nullable()->after('doctor_id')->constrained()->onDelete('set null');
                // ->nullable() : permet à la colonne d'être vide (un utilisateur peut ne pas être un infirmier).
                // ->after('doctor_id') : positionne la colonne juste après 'doctor_id' (ajustez si nécessaire).
                // ->constrained() : assume que la table de référence est 'nurses'.
                // ->onDelete('set null') : si l'infirmier est supprimé, la clé étrangère dans 'users' devient NULL.
            }

            // Si vous aviez d'autres clés étrangères que vous avez ajoutées manuellement, vous pouvez les gérer ici.
            // Par exemple, si vous voulez mettre 'patient_id' ici:
            // if (!Schema::hasColumn('users', 'patient_id')) {
            //     $table->foreignId('patient_id')->nullable()->after('nurse_id')->constrained()->onDelete('set null');
            // }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Suppression de la clé étrangère
            $table->dropConstrainedForeignId('nurse_id');

            // 2. Suppression de la colonne
            $table->dropColumn('nurse_id');
        });
    }
};
