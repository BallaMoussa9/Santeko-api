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
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable()->after('doctor_id');
            $table->unsignedBigInteger('consultation_id')->nullable()->after('patient_id');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['patient_id']);
            $table->dropColumn('doctor_id');
            $table->dropColumn('patient_id');
            $table->dropForeign(['consultation_id']);
            $table->dropColumn('consultation_id');
        });
    }
};
