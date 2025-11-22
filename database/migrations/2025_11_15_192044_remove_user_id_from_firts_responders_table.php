<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🚨 IMPORTANT : Supprimer d'abord la contrainte de clé étrangère
        Schema::table('firts_responders', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Supprime la contrainte
            $table->dropColumn('user_id'); // Supprime la colonne
        });
    }

    public function down(): void
    {
        // 🚨 ATTENTION : Reconstruire la colonne en cas de rollback
        Schema::table('firts_responders', function (Blueprint $table) {
            // Recréer la colonne (assurez-vous que le type est correct)
            $table->bigInteger('user_id')->unsigned()->nullable()->after('id');
            
            // Recréer la contrainte si nécessaire (cela dépend de vos anciennes migrations)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
