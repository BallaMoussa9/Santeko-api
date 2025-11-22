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
        Schema::table('firts_responders', function (Blueprint $table) {
            // ✅ Ajoute la colonne user_id comme clé étrangère
            // J'utilise nullable() pour permettre d'ajouter des FirstResponders sans les lier immédiatement
            // à un utilisateur si nécessaire, mais si elle doit être obligatoire, retirez nullable().
            $table->foreignId('user_id')
                  ->nullable() 
                  ->constrained('users') // Lie à la table 'users'
                  ->onDelete('cascade') // Supprime le FirstResponder si l'utilisateur est supprimé
                  ->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('firts_responders', function (Blueprint $table) {
            // ✅ Retire la clé étrangère et la colonne en cas de rollback
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};