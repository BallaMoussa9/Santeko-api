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
        Schema::table('statistiqueregionales', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedBigInteger('department_id')->nullable()->after('user_id');
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
        Schema::table('statistiqueregionales', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['user_id', 'department_id']);
        });
    }
};
