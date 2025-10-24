<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les modifications sur la table appointments.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Changer appointment_date pour ne garder que la date (optionnel si déjà datetime)
            $table->date('appointment_date')->change();

            // Changer appointment_time de datetime à time
            $table->time('appointment_time')->nullable()->change();
        });
    }

    /**
     * Revenir en arrière si nécessaire.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Revenir à datetime si nécessaire
            $table->dateTime('appointment_date')->change();
            $table->dateTime('appointment_time')->nullable()->change();
        });
    }
};
