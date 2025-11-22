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
            // Ajout de la colonne first_responder_id
            $table->foreignId('first_responder_id')
                  ->nullable()
                  ->after('nurse_id') // Placez-le après nurse_id ou où vous voulez
                  ->constrained('firts_responders') // La table est 'firts_responders'
                  ->onDelete('set null'); // Optionnel: Si l'urgentiste est supprimé, l'ID dans users devient NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la clé étrangère en premier
            $table->dropConstrainedForeignId('first_responder_id');

            // Supprimer la colonne
            $table->dropColumn('first_responder_id');
        });
    }
};