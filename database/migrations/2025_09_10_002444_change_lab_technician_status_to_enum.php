// database/migrations/<timestamp>_change_lab_technician_status_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labtechnicians', function (Blueprint $table) {
            $table->enum('status', ['active', 'on_leave', 'resigned', 'suspended'])->default('active')->change();
        });
    }

    public function down(): void
    {
        Schema::table('lab_technicians', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });
    }
};
