// database/migrations/<timestamp>_change_invoice_status_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
