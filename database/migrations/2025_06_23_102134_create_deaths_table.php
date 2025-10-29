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
        Schema::create('deaths', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_deces')->nullable();
            $table->string('lieu_deces')->nullable();
            $table->string('cause_deces')->nullable();
            $table->text('circonstances_deces')->nullable();
            $table->enum('statut', ['pending', 'confirmed', 'canceled'])->default('pending')->nullable();
            $table->string('numero_acte_deces')->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deaths');
    }
};
