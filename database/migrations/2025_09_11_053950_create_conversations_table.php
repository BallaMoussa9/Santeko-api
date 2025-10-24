<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Pour les chats de groupe, par exemple
            $table->boolean('is_private')->default(true); // Vrai pour les 1-to-1
            $table->timestamps();
        });

        // Table pivot pour relier les utilisateurs et les conversations
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->primary(['conversation_id', 'user_id']); // Clé primaire composée
            $table->timestamp('last_read_at')->nullable(); // Pour le statut de lecture
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_user');
        Schema::dropIfExists('conversations');
    }
};