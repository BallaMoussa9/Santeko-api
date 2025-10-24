<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Supprimer d'abord les contraintes de clé étrangère
            if (Schema::hasColumn('messages', 'admin_id')) {
                $table->dropForeign(['admin_id']);
            }
            if (Schema::hasColumn('messages', 'recever_id')) {
                $table->dropForeign(['recever_id']);
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            // Puis supprimer les colonnes
            $table->dropColumn([
                'admin_id',
                'recever_id',
                'status',
                'priority',
                'start_time',
                'end_time',
                'title'
            ]);
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Recréer les colonnes
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('recever_id')->nullable();
            $table->string('title')->nullable();
            $table->enum('status', ['sent', 'delivered', 'read'])->default('sent');
            $table->string('priority')->nullable()->default('normal');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            // Recréer les contraintes si nécessaire
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recever_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
