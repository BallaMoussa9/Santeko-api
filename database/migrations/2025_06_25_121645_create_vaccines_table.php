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
        Schema::create('vaccines', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->string('fabricant')->nullable();
            $table->string('code')->unique()->nullable();
            $table->string('type')->nullable();
            $table->integer('duree_validite')->nullable(); // en mois
            $table->text('description')->nullable();
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccines');
    }
};
