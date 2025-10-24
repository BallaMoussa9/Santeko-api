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
        Schema::table('consultation_historys', function (Blueprint $table) {
            $table->unsignedBigInteger('medicalrecored_id')->nullable()->after('id');
            $table->unsignedBigInteger('consultation_id')->nullable()->after('medicalrecored_id');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
            $table->unsignedBigInteger('patient_id')->nullable()->after('consultation_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->unsignedBigInteger('department_id')->nullable()->after('patient_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('medicalrecored_id')->references('id')->on('medicalrecords')->onDelete('cascade');
            $table->unsignedBigInteger('doctor_id')->nullable()->after('department_id');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultation_historys', function (Blueprint $table) {
            $table->dropForeign(['medicalrecored_id']);
            $table->dropColumn(['medicalrecored_id', 'consultation_id', 'department_id', 'doctor_id', 'patient_id']);
            $table->dropForeign(['consultation_id']);
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['doctor_id']);
        });
    }
};
