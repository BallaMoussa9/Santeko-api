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
        Schema::table('births', function (Blueprint $table) {

            $table->unsignedBigInteger('patient_id')->nullable()->after('id');
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade');
            $table->unsignedBigInteger('doctor_id')->nullable()->after('patient_id');
            $table->foreign('doctor_id')
                ->references('id')
                ->on('doctors')
                ->onDelete('cascade');

            $table->unsignedBigInteger('department_id')->nullable()->after('doctor_id');
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
            $table->unsignedBigInteger('nurse_id')->nullable();
            $table->foreign('nurse_id')->references('id')->on('nurses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('births', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['nurse_id']);
            $table->dropColumn('patient_id');
            $table->dropForeign(['doctor_id']);
            $table->dropColumn('doctor_id');
            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id','nurse_id']);
        });
    }
};
