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
        Schema::table('analyses', function (Blueprint $table) {
            $table->unsignedBigInteger('analyses_request_id')->nullable()->after('id');
            $table->foreign('analyses_request_id')
                ->references('id')
                ->on('analysesrequest')
                ->onDelete('cascade');
            $table->unsignedBigInteger('laboratory_id')->nullable()->after('analyses_request_id');
            $table->foreign('laboratory_id')
                ->references('id')
                ->on('laboratorys')
                ->onDelete('cascade');
            $table->unsignedBigInteger('patient_id')->nullable()->after('laboratory_id');
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade');
            $table->unsignedBigInteger('consultation_id')->nullable()->after('patient_id');
            $table->foreign('consultation_id')
                ->references('id')
                ->on('consultations')
                ->onDelete('cascade');
            $table->unsignedBigInteger('labtechnicians_id')->nullable()->after('consultation_id');
            $table->foreign('labtechnicians_id')
                ->references('id')
                ->on('labtechnicians')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropForeign(['analyses_request_id']);
            $table->dropForeign(['laboratory_id']);
            $table->dropForeign(['patient_id']);
            $table->dropColumn(['analyses_request_id', 'laboratory_id', 'patient_id']);
            $table->dropForeign(['consultation_id']);
            $table->dropColumn('consultation_id');
            $table->dropForeign(['labtechnicians_id']);
            $table->dropColumn('labtechnicians_id');
        });
    }
};
