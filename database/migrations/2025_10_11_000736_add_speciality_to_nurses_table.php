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
        Schema::table('nurses', function (Blueprint $table) {
            // Ajoute la nouvelle colonne 'speciality' après 'department_id'.
            // Nous utilisons 'string' (VARCHAR) pour stocker le nom de la spécialité.
            $table->string('speciality')->nullable()->after('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nurses', function (Blueprint $table) {
            // Inverse l'opération en supprimant la colonne 'speciality'.
            $table->dropColumn('speciality');
        });
    }
};
