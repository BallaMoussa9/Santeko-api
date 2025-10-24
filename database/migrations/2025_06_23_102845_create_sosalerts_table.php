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
        Schema::create('sosalerts', function (Blueprint $table) {
            $table->id();
            $table->enum('status',['en attente','annule','traiteß'])->nullable();
            $table->string('type')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('initiated_at')->nullable();
            $table->dateTime('responded_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sosalerts');
    }
};
