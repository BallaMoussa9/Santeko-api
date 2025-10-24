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
      Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique()->nullable();
    $table->string('display_name')->unique()->nullable();
    $table->string('description')->nullable();
    $table->enum('type', [
        'admin',
        'super_admin',
        'infirmier',
        'doctor',
        'emergency_doctor', // 👈 ajouté ici
        'nurse',
        'pharmacist',
        'secretary',
        'accountant',
        'patient',
        'comms_officer',
        'lab_technician',
        'lab_manager',
        'lab_assistant',
        'lab_supervisor',
        'pathologist',
        'paramedic'
    ])->default('patient');
    $table->timestamps();
});



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
