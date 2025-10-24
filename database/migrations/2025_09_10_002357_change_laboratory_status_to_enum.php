// database/migrations/<timestamp>_change_laboratory_status_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laboratorys', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'under_maintenance'])->default('active')->change();
        });
    }

    public function down(): void
    {
        Schema::table('laboratories', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });
    }
};
