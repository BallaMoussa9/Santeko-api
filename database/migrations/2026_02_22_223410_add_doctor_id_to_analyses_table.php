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
        Schema::table('analyses', function (Blueprint $table) {
            // Ajoute la colonne doctor_id (clé étrangère vers la table doctors)
            $table->foreignId('doctor_id')->nullable()->constrained()->onDelete('set null');
            
            // Ajoute la colonne requested_at (date de la demande)
            $table->timestamp('requested_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            // Supprime les colonnes et la contrainte de clé étrangère
            $table->dropForeign(['doctor_id']);
            $table->dropColumn(['doctor_id', 'requested_at']);
        });
    }
};