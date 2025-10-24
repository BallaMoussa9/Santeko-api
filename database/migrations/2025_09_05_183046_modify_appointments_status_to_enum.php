<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // N'oubliez pas ceci !

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ATTENTION : Cette opération PEUT entraîner des pertes de données si
        // les valeurs existantes dans 'status' ne correspondent pas
        // aux nouvelles valeurs de l'ENUM.
        Schema::table('appointments', function (Blueprint $table) {
            // S'assurer que les valeurs existantes sont compatibles avec l'ENUM avant le changement.
            // Par exemple, si vous aviez des statuts comme 'pending_review',
            // vous devrez les mettre à jour en 'pending' AVANT cette migration.
            // Sinon, les lignes avec des valeurs non valides pourraient être définies à NULL ou à la valeur par défaut.

            $table->enum('status', ['pending', 'confirmed', 'canceled', 'rescheduled', 'completed'])
                  ->default('pending')
                  ->change(); // <-- C'est la méthode clé
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pour le rollback, nous devons revenir à l'ancien type (probablement string).
        Schema::table('appointments', function (Blueprint $table) {
            // ATTENTION : Ici aussi, les valeurs ENUM doivent être compatibles
            // avec le type 'string' (ce qui est généralement le cas).
            // Mais si vous aviez des contraintes plus complexes sur l'ancien 'string',
            // il faudrait les recréer.
            $table->string('status') // Supposons que l'ancien type était string
                  ->nullable() // Assurez-vous de définir la nullabilité et la valeur par défaut d'origine si elles existaient
                  ->change();
        });
    }
};
