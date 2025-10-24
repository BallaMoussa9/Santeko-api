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
        Schema::create('hospital_patient', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->date('date_entree')->nullable();
            $table->date('date_sortie')->nullable();
            $table->string('motif')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_patient');
    }
};
