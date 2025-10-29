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
        Schema::create('medicalrecords', function (Blueprint $table) {
            $table->id();
            $table->string('blood_type')->nullable();
            $table->string('treatment_plan')->nullable();
            $table->string('diagnosis')->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->enum('status', ['active', 'inactive'])->nullable();
            $table->unsignedBigInteger('hospital_id')->nullable();
            $table->string('numero_dossier')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicalrecords');
    }
};
