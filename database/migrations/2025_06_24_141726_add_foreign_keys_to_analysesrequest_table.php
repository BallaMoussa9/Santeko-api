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
        Schema::table('analysesrequest', function (Blueprint $table) {
            $table->unsignedBigInteger('analyses_id')->nullable()->after('id');
            $table->foreign('analyses_id')->references('id')->on('analyses')->onDelete('cascade');
            $table->unsignedBigInteger('patient_id')->nullable()->after('analyses_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->unsignedBigInteger('labtechnician_id')->nullable()->after('patient_id');
            $table->foreign('labtechnician_id')->references('id')->on('labtechnicians')->onDelete('cascade');
            $table->unsignedBigInteger('lab_id')->nullable()->after('labtechnician_id');
            $table->foreign('lab_id')->references('id')->on('laboratorys')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analysisrequest', function (Blueprint $table) {
            $table->dropForeign(['analyses_id']);
            $table->dropColumn('analyses_id');
            $table->dropForeign(['patient_id']);
            $table->dropColumn('patient_id');
            $table->dropForeign('lab_id');
            $table->dropForeign(['labtechnician_id']);
            $table->dropColumn(['doctor_id','labtechnician_id','lab_id']);
        });
    }
};
