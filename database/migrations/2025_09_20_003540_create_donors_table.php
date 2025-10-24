<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            // Supprime la contrainte de clé étrangère
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('blood_group', 5);
            $table->string('rh_factor', 10);
            $table->date('date_of_birth');
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
