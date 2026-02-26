<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajout de la clé étrangère vers la table labtechnicians
            $table->foreignId('lab_technician_id')
                  ->nullable()
                  ->constrained('labtechnicians') 
                  ->onDelete('set null')
                  ->after('nurse_id'); // Placé logiquement après les autres rôles de santé
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['lab_technician_id']);
            $table->dropColumn('lab_technician_id');
        });
    }
};