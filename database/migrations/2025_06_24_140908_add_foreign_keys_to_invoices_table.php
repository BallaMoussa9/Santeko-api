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
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('consultation_id')->nullable()->after('id');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
            $table->unsignedBigInteger('payments_id')->nullable()->after('consultation_id');
            $table->foreign('payments_id')->references('id')->on('payments')->onDelete('cascade');
            $table->unsignedBigInteger('patient_id')->nullable()->after('payments_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable()->after('patient_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['consultation_id']);
            $table->dropColumn(['consultation_id', 'payments_id', 'patient_id', 'user_id']);
            $table->dropForeign(['payments_id']);
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['user_id']);

        });
    }
};
