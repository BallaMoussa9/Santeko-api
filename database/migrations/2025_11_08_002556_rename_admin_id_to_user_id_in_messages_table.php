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
        Schema::table('messages', function (Blueprint $table) {
            // Renomme la colonne existante 'admin_id' en 'user_id'
            $table->renameColumn('admin_id', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Annule l'action : Renomme 'user_id' en 'admin_id'
            $table->renameColumn('user_id', 'admin_id');
        });
    }
};