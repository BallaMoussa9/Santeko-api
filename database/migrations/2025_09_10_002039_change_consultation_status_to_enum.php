// database/migrations/<timestamp>_change_consultation_status_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled')->change();
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->string('status')->default('scheduled')->change();
        });
    }
};
