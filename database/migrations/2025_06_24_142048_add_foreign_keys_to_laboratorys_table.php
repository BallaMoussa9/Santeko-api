<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laboratorys', function (Blueprint $table) {
            $table->unsignedBigInteger('labtech_id')->nullable()->after('id');
            $table->foreign('labtech_id')
                ->references('id')
                ->on('labtechnicians')
                ->onDelete('cascade');
            $table->unsignedBigInteger('department_id')->nullable()->after('labtech_id');
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratorys', function (Blueprint $table) {
            $table->dropForeign(['labtech_id']);
            $table->dropColumn(['labtech_id', 'department_id']);
            $table->dropForeign(['department_id']);
        });
    }
};
