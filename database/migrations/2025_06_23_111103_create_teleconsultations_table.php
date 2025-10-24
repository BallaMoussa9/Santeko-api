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
        Schema::create('teleconsultations', function (Blueprint $table) {
            $table->id();
            $table->dateTime('scheduled_at')->nullable();
            $table->integer('duration')->nullable();
            $table->string('status')->default('pending')->nullable();
            $table->string('consultation_link')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teleconsultations');
    }
};
