<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            $table->enum('status', ['successful', 'failed', 'in_progress', 'pending'])->default('pending')->after('updated_at');
            $table->string('filename')->nullable()->after('status');
            $table->string('path')->nullable()->after('filename');
            $table->unsignedBigInteger('size')->nullable()->comment('Size in bytes')->after('path');
            $table->enum('type', ['database', 'files', 'full'])->default('database')->after('size');
            $table->timestamp('last_run_at')->nullable()->after('type'); // Quand la sauvegarde a été réellement lancée
            $table->text('notes')->nullable()->after('last_run_at'); // Pour des notes ou messages d'erreur
        });
    }

    public function down(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            $table->dropColumn(['status', 'filename', 'path', 'size', 'type', 'last_run_at', 'notes']);
        });
    }
};