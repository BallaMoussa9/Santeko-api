// database/migrations/<timestamp>_change_sauvegardes_status_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sauvegardes', function (Blueprint $table) {
            $table->enum('status', ['successful', 'failed', 'in_progress', 'scheduled'])->default('successful')->change();
        });
    }

    public function down(): void
    {
        Schema::table('sauvegardes', function (Blueprint $table) {
            // Revenir à un type de colonne générique pour ne pas perdre de données.
            $table->string('status')->default('successful')->change();
        });
    }
};