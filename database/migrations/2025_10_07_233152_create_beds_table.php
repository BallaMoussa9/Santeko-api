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
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->string('bed_number')->comment('Ex: L1, L2, A pour un lit simple.');

            // Clé étrangère vers la chambre
            $table->foreignId('room_id')->constrained()->onDelete('cascade');

            $table->enum('status', ['available', 'occupied', 'cleaning', 'maintenance'])->default('available');
            $table->boolean('is_private')->default(false)->comment('Si le lit est dans une chambre privée ou partagée');
            $table->string('equipment_notes')->nullable()->comment('Ex: présence d un moniteur, oxygene');

            // Peut lier directement au patient actuellement hospitalisé (relation 1:1)
            $table->foreignId('patient_id')->nullable()->unique()->constrained()->onDelete('set null');

            $table->timestamps();

            // S'assurer que le numéro de lit est unique PAR chambre
            $table->unique(['room_id', 'bed_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
