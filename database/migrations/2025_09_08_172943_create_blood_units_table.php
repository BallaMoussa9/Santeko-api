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
        Schema::create('blood_units', function (Blueprint $table) {
            $table->id();
            $table->string('blood_group', 5); // Ex: A, B, AB, O
            $table->string('rh_factor', 10);  // Ex: Positif, Négatif
            $table->string('unit_number')->unique();
            $table->date('collection_date');
            $table->date('expiration_date');
            $table->enum('status', ['available', 'used', 'expired', 'quarantined'])->default('available');
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_units');
    }
};
