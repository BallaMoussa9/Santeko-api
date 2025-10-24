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
        Schema::create('analysesrequest', function (Blueprint $table) {
            $table->id();
            $table->text('result_text')->nullable();
            $table->string('result_file')->nullable();
            $table->string('status')->nullable();
            $table->string('analyse_type')->nullable();
            $table->dateTime('validated_at')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysisrequest');
    }
};
