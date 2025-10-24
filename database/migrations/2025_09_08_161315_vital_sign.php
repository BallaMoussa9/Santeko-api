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
         Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('nurse_id')->constrained('users')->onDelete('cascade'); // Lié à la table users
            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->integer('heart_rate')->nullable(); // Battements par minute
            $table->decimal('temperature', 4, 1)->nullable(); // Ex: 37.5
            $table->integer('respiratory_rate')->nullable(); // Respiration par minute
            $table->decimal('oxygen_saturation', 4, 1)->nullable(); // SpO2, Ex: 98.5
            $table->decimal('weight', 6, 2)->nullable(); // Kg, Ex: 75.20
            $table->decimal('height', 5, 2)->nullable(); // Mètres, Ex: 1.75
            $table->dateTime('recorded_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::dropIfExists('vital_signs');
    }
};
