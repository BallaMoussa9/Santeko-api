<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddInProgressStatusToSosalertsEnum extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE sosalerts 
            MODIFY status ENUM('en attente', 'annule', 'traite', 'in_progress') NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE sosalerts 
            MODIFY status ENUM('en attente', 'annule', 'traite') NULL
        ");
    }
}
