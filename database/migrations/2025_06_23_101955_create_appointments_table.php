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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->dateTime('appointment_date')->nullable();
            $table->string('appointment_time')->nullable();
            $table->string('type')->nullable();
            $table->string('motif')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'canceled', 'rescheduled', 'completed'])->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
