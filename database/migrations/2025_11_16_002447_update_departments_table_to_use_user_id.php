<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            
            // 🚨 CORRECTION ÉTAPE 1: Supprimer la contrainte de clé étrangère
            // La syntaxe la plus sûre est de supprimer d'abord la clé, puis la colonne.
            // Le nom de la clé étrangère est 'departments_doctor_id_foreign' par convention.
            $table->dropForeign(['doctor_id']); 
            
            // 🚨 CORRECTION ÉTAPE 2: Supprimer la colonne
            $table->dropColumn('doctor_id');

            // 3. Ajout de la nouvelle colonne user_id (clé étrangère vers la table 'users')
            $table->foreignId('user_id') 
                  ->nullable()           
                  ->after('admin_id')    
                  ->constrained()        
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Suppression de la colonne user_id et de sa contrainte
            $table->dropConstrainedForeignId('user_id');

            // Ré-ajout de l'ancienne colonne doctor_id (pour le rollback)
            $table->unsignedBigInteger('doctor_id')->nullable()->after('admin_id');
            // Ré-ajout de la contrainte (si elle existait)
            // $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('set null'); 
        });
    }
};