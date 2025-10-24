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
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->after('id');
            $table->unsignedBigInteger('patient_id')->nullable()->after('account_id');
            $table->unsignedBigInteger('invoice_id')->nullable()->after('patient_id');

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accountants')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn(['account_id', 'patient_id',]);
            $table->dropForeign(['patient_id']);

        });
    }
};
