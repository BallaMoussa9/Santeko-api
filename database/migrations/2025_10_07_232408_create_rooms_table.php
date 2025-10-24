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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique()->comment('Ex: A101, C205');
            $table->string('floor')->nullable();

            // 🔑 CHANGEMENT : Remplacer 'service_id' par 'department_id'
            // Assurez-vous que cette migration s'exécute APRÈS celle de 'departments'
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');

            $table->integer('capacity')->default(1)->comment('Nombre de lits maximum');
            $table->enum('type', ['simple', 'double', 'suite', 'isolation'])->default('simple');
            $table->boolean('is_available')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
