<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Nécessaire pour les requêtes directes

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Étape 1 : Supprimer la colonne 'nurse_id' si elle existe.
        // Puisqu'il n'y a pas de FK liée par le nom par défaut de Laravel,
        // un simple `dropColumn` devrait fonctionner.
        Schema::table('nurse_activity_reports', function (Blueprint $table) {
            if (Schema::hasColumn('nurse_activity_reports', 'nurse_id')) {
                // Si la colonne existe, nous devons la supprimer.
                // Attention : Si des données importantes sont dans cette colonne,
                // vous devriez les sauvegarder avant cette étape.
                $table->dropColumn('nurse_id');
                echo "Colonne 'nurse_id' supprimée de 'nurse_activity_reports'.\n";
            }
        });

        // ----------------------------------------------
        // Étape 2 : Nettoyage des données (CRUCIALE AVANT D'AJOUTER LA FK)
        // Ceci est une précaution. Au cas où votre `nurse_id` existant avait des valeurs qui ne correspondaient pas
        // et qu'on aurait pu contourner le `dropColumn` d'une manière ou d'une autre (peu probable ici).
        // Mais c'est une bonne pratique de s'assurer de l'intégrité avant d'ajouter une FK.
        // Cependant, comme on a supprimé la colonne, cette partie est techniquement moins critique,
        // mais le fait est que vos *données de rapports d'activités* DOIVENT faire référence à un `nurse_id` valide.
        // Si vous recréez la colonne et qu'ensuite vous y mettez des données qui étaient là avant (ce que cette migration ne fait pas),
        // alors cette étape serait cruciale. Pour l'instant, c'est surtout un rappel d'intégrité future.
        // Pour être sûr, la meilleure chose est de s'assurer que si vous aviez des rapports
        // avec des nurse_id non valides, ils seraient gérés.

        // Si des rapports sans nurse_id associé existaient, la recréation de la colonne
        // `foreignId` (qui est NOT NULL par défaut) échouerait sans cela.
        // On va s'assurer que la colonne `nurse_id` sera créée et remplie *correctement* ensuite.
        // Pour cette migration, on part du principe que la colonne est vide après `dropColumn`.
        // Donc, il n'y a rien à nettoyer sur la colonne `nurse_id` elle-même pour le moment,
        // car elle vient d'être supprimée.

        // Si vous avez des données de `nurse_id` à conserver et à "corriger" après la suppression de la colonne,
        // c'est ici que vous devriez les réinsérer avec les bons IDs.
        // EXEMPLE (SI VOUS AVEZ SAUVEGARDÉ LES ANCIENS nurse_id ET VOULEZ LES REMETTRE À JOUR) :
        // DB::table('nurse_activity_reports')
        //    ->where('old_nurse_id', 'IN', [IDs invalides])->update(['nurse_id' => NULL]); // Ou delete
        // ----------------------------------------------

        // Étape 3 : Recréer la colonne 'nurse_id' avec la clé étrangère correcte
        Schema::table('nurse_activity_reports', function (Blueprint $table) {
            // foreignId() crée une colonne BIGINT UNSIGNED et la rend NOT NULL par défaut,
            // ce qui correspond à la structure que vous avez montrée.
            $table->foreignId('nurse_id')
                  ->constrained('nurses') // <-- Cible la table 'nurses'
                  ->onDelete('cascade')
                  ->after('patient_id'); // Positionne après patient_id, ajustez si besoin
            echo "Colonne 'nurse_id' recréée avec clé étrangère vers 'nurses.id'.\n";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nurse_activity_reports', function (Blueprint $table) {
            // D'abord, supprimez la clé étrangère pour éviter les erreurs
            $table->dropForeign(['nurse_id']);
            echo "Clé étrangère 'nurse_activity_reports_nurse_id_foreign' supprimée.\n";

            // Ensuite, supprimez la colonne 'nurse_id'
            $table->dropColumn('nurse_id');
            echo "Colonne 'nurse_id' supprimée de 'nurse_activity_reports' (rollback).\n";

            // Si vous voulez recréer l'ancienne colonne sans FK pour un rollback complet
            // $table->bigInteger('nurse_id')->unsigned()->nullable()->after('patient_id');
        });
    }
};
