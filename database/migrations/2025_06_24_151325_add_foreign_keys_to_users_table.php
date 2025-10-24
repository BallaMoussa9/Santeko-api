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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('language_id')->nullable()->after('id');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->unsignedBigInteger('role_id')->nullable()->after('language_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->unsignedBigInteger('department_id')->nullable()->after('role_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropColumn('language_id');
        });
    }
};
