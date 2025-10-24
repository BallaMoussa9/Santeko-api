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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->enum('status', ['sent', 'delivered', 'read'])->default('sent')->nullable();
            $table->string('priority')->default('normal')->nullable();
            $table->dateTime('start_time')->nullable();//date d'affichage
            $table->dateTime('end_time')->nullable();//date de fin d'affichage

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
