<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Supprime la contrainte de clé étrangère pointant vers la table 'admins'
            // Le nom de la clé étrangère est généralement 'tablename_columnname_foreign'.
            // On utilise le nom fourni dans l'erreur : messages_admin_id_foreign
            $table->dropForeign('messages_admin_id_foreign');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // On la recrée si on annule la migration (attention au nom de la table référencée !)
            $table->foreign('user_id', 'messages_admin_id_foreign')->references('id')->on('admins')->onDelete('cascade');
        });
    }
};
