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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->default('string')->nullable(); // e.g., string, integer
            $table->string('category')->default('general')->nullable(); // e.g., general, security, appearance
            $table->string('status')->default('active')->nullable(); // e.g., active, inactive
            $table->boolean('is_editable')->default(true)->nullable(); // Indicates if the setting can be edited by users
            $table->boolean('is_visible')->default(true)->nullable(); // Indicates if the setting is visible
            $table->boolean('is_required')->default(false)->nullable(); // Indicates if the setting is required
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
