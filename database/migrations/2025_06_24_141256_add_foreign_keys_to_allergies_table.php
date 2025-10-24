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
        Schema::table('allergies', function (Blueprint $table) {
            $table->unsignedBigInteger('medical_record_id')->nullable()->after('id');
            $table->foreign('medical_record_id')->references('id')->on('medicalrecords')->onDelete('cascade');
            $table->unsignedBigInteger('patient_id')->nullable()->after('medical_record_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allergies', function (Blueprint $table) {
            $table->dropForeign(['medical_record_id']);
            $table->dropColumn('medical_record_id');
        });
    }
};
