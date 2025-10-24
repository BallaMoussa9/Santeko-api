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
        Schema::table('sosalerts', function (Blueprint $table) {
            $table->unsignedBigInteger('first_responder_id')
                  ->nullable();
            $table->foreign('first_responder_id')->references('id')->on('firts_responders')->onDelete('cascade');       // Assuming 'status' is the last column in the sosalerts table

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sosalerts', function (Blueprint $table) {
            $table->dropForeign(['first_responder_id']);
            $table->dropColumn('first_responder_id');
        });
    }
};
