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
            $table->unsignedBigInteger('vaccine_id')->nullable()->after('id');
            $table->foreign('vaccine_id')->references('id')->on('vaccines')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->dropForeign(['vaccine_id']);
            $table->dropColumn('vaccine_id');
        });
    }
};
