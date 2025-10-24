// database/migrations/<timestamp>_change_regional_statistic_status_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('statistiqueregionales', function (Blueprint $table) {
            $table->enum('status', ['published', 'draft', 'archived'])->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('regional_statistics', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }
};
