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
        Schema::create('firts_responders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique()->nullable(); // Assure that each user can only be a first responder once
            $table->string('speciality')->nullable();
            $table->string('status')->default('available')->nullable(); // available, on_call, unavailable
            $table->string('location')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firts_responders');
    }
};
