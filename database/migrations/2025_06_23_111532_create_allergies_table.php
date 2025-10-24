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
        Schema::create('allergies', function (Blueprint $table) {
            $table->id();
            $table->string('substance')->nullable();
            $table->string('reaction_description')->nullable();
            $table->string('severity')->nullable();
            $table->string('recorded_by')->nullable();
            $table->string('status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergies');
    }
};
