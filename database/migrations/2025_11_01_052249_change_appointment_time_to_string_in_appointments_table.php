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
        Schema::table('appointments', function (Blueprint $table) {
            // Change le type de appointment_time en VARCHAR pour accepter le format HH:MM
            $table->string('appointment_time', 8)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Si vous voulez revenir en arrière (optionnel)
            $table->time('appointment_time')->nullable()->change();
        });
    }
};
