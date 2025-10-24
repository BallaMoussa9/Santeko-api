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
        // Renomme la table 'medicalreports' en 'medical_reports'
        Schema::rename('medicalreports', 'medical_reports');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Si vous voulez revenir en arrière, renommez la table en 'medicalreports'
        Schema::rename('medical_reports', 'medicalreports');
    }
};
