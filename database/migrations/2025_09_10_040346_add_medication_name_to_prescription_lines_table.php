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
        Schema::table('prescription_lines', function (Blueprint $table) {
            // Ajout de la colonne pour le nom du médicament
            $table->string('medication_name')->after('prescription_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_lines', function (Blueprint $table) {
            $table->dropColumn('medication_name');
        });
    }
};