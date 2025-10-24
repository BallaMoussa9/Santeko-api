<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la contrainte avec le vrai nom
            $table->dropForeign('users_docteur_id_foreign');

            // Recréer la bonne FK vers doctors
            $table->foreign('doctor_id')
                  ->references('id')
                  ->on('doctors')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);

            // Revenir à l'ancien FK vers users
            $table->foreign('doctor_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }
};
