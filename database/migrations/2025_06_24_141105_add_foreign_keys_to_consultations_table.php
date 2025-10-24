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
        Schema::table('consultations', function (Blueprint $table) {
            $table->unsignedBigInteger('prescription_id')->nullable()->after('id');
            $table->unsignedBigInteger('doctor_id')->nullable()->after('prescription_id');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->unsignedBigInteger('patient_id')->nullable()->after('doctor_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('prescription_id')->references('id')->on('prescriptions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['prescription_id']);
            $table->dropColumn(['prescription_id', 'doctor_id', 'patient_id']);
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['patient_id']);
        });
    }
};
