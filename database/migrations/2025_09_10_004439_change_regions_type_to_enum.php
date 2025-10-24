// database/migrations/<timestamp>_change_regions_type_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            // J'assume des types comme 'urban', 'rural', 'coastal', 'mountainous'
            // Adaptez ces valeurs à ce qui est pertinent pour votre application
            $table->enum('type', ['urban', 'rural', 'coastal', 'mountainous', 'other'])->default('urban')->change();
        });
    }

    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            // Revenir à un type de colonne générique pour ne pas perdre de données.
            $table->string('type')->default('urban')->change();
        });
    }
};
