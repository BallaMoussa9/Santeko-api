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
        Schema::create('consultation_historys', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_consultation')->nullable();
            $table->string('type')->nullable();
            $table->string('motif')->nullable();
            $table->text('diagnostic')->nullable();
            $table->text('traitement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_consultation_historys');
    }
};
