// database/migrations/<timestamp>_change_analyserequest_status_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analysesrequest', function (Blueprint $table) {
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('analyse_requests', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
