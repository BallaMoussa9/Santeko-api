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
            $table->unsignedBigInteger('region_id')->nullable()->after('id');
            $table->foreign('region_id')
                ->references('id')
                ->on('regions')
                ->onDelete('cascade');
            $table->unsignedBigInteger('hopital_id')->nullable()->after('user_id');
            $table->foreign('hopital_id')
                ->references('id')
                ->on('hospitals')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statistiqueregionales', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
            $table->dropForeign(['hopital_id']);
            $table->dropColumn('hopital_id');
        });
    }
};
