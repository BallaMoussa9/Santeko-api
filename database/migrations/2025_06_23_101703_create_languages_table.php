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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->nullable();
            $table->string('code')->unique()->nullable();
            $table->string('locale')->nullable();
            $table->string('direction')->nullable(); // 'ltr' for left-to-right, 'rtl' for right-to-left
            $table->boolean('is_active')->default(true)->nullable(); // Indicates if the language is active
            $table->boolean('is_default')->default(false)->nullable(); // Indicates if the language is the default one
            $table->string('native_name')->nullable(); // Path to the flag icon image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
