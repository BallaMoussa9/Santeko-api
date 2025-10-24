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
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->unsignedBigInteger('medicalrecord_id')->nullable()->after('id');
            $table->unsignedBigInteger('nurse_id')->nullable()->after('medicalrecord_id');
            $table->foreign('nurse_id')->references('id')->on('nurses')->onDelete('cascade');
            $table->foreign('medicalrecord_id')->references('id')->on('medicalrecords')->onDelete('cascade');
            $table->unsignedBigInteger('patient_id')->nullable()->after('nurse_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->dropForeign(['medicalrecord_id']);
            $table->dropColumn(['medicalrecord_id', 'nurse_id']);
            $table->dropForeign(['nurse_id']);
        });
    }
};
