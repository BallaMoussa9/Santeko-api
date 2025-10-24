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
        // On suppose que docteur_id est une relation vers users ou une autre table (ex: doctors)
        $table->unsignedBigInteger('docteur_id')->nullable()->after('id');

        // Si docteur_id pointe vers la même table users
        $table->foreign('docteur_id')->references('id')->on('users')->onDelete('set null');

        // Si docteur_id pointe vers une table doctors :
        // $table->foreign('docteur_id')->references('id')->on('doctors')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['docteur_id']);
        $table->dropColumn('docteur_id');
    });
}
};
