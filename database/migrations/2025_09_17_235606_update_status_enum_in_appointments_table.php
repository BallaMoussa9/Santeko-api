<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE appointments
            MODIFY status ENUM('pending','confirmed','canceled','rescheduled','completed','scheduled')
            NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE appointments
            MODIFY status ENUM('pending','confirmed','canceled','rescheduled','completed')
            NOT NULL DEFAULT 'pending'");
    }
};
