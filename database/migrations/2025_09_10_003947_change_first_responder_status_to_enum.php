// database/migrations/<timestamp>_change_first_responder_status_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('firts_responders', function (Blueprint $table) {
            $table->enum('status', ['available', 'on_duty', 'off_duty', 'suspended'])->default('available')->change();
        });
    }

    public function down(): void
    {
        Schema::table('first_responders', function (Blueprint $table) {
            // Revenir à un type de colonne générique pour ne pas perdre de données.
            $table->string('status')->default('available')->change();
        });
    }
};
