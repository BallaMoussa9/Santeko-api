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
        Schema::create('laboratorys', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->nullable()->comment('Name of the laboratory');
            $table->string('adress')->nullable()->comment('Address of the laboratory');
            $table->string('phone')->nullable()->comment('Phone number of the laboratory');
            $table->string('email')->nullable()->comment('Email address of the laboratory');
            $table->time('opening_time')->nullable()->comment('Opening time of the laboratory');
            $table->time('closing_time')->nullable()->comment('Closing time of the laboratory');
            $table->string('status')->default('active')->nullable()->comment('Status of the laboratory (active, inactive)');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratorys');
    }
};
