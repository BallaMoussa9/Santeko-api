// database/migrations/<timestamp>_change_death_status_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deaths', function (Blueprint $table) {
            $table->enum('statut', ['recorded', 'verified', 'archived'])->default('recorded')->change();
        });
    }

    public function down(): void
    {
        Schema::table('deaths', function (Blueprint $table) {
            $table->string('status')->default('recorded')->change();
        });
    }
};
