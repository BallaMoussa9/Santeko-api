<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier que la table appointments existe
        if (!Schema::hasTable('appointments')) {
            return;
        }

        // Récupérer la structure actuelle
        $columns = Schema::getColumnListing('appointments');

        // Créer une nouvelle table temporaire
        Schema::create('appointments_temp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->dateTime('date')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'canceled', 'rescheduled', 'completed'])->default('pending');
            $table->timestamps();
        });

        // Copier les données (si les colonnes existent)
        $existingColumns = collect(['id', 'patient_id', 'doctor_id', 'date', 'status', 'created_at', 'updated_at'])
            ->filter(fn($col) => in_array($col, $columns))
            ->implode(', ');

        if ($existingColumns) {
            DB::statement("INSERT INTO appointments_temp ($existingColumns) SELECT $existingColumns FROM appointments");
        }

        // Supprimer l'ancienne table
        Schema::drop('appointments');

        // Renommer la nouvelle table
        Schema::rename('appointments_temp', 'appointments');
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
