// database/migrations/<timestamp>_change_allergie_status_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('allergies', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'resolved'])->default('active')->change();
        });
    }

    public function down(): void
    {
        Schema::table('allergies', function (Blueprint $table) {
            // Revenir à un type de colonne générique pour ne pas perdre de données.
            $table->string('status')->default('active')->change();
        });
    }
};
