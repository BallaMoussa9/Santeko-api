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
        Schema::create('statistiqueregionales', function (Blueprint $table) {
            $table->id();
            $table->string('region')->nullable();
            $table->date('period_start')->nullable();;
            $table->date('period_end')->nullable();;
            $table->integer('total_consultations')->nullable();;
            $table->integer('total_teleconsultations')->nullable();;
            $table->integer('total_analyses')->nullable();;
            $table->float('taux_prescriptions')->nullable();;
            $table->integer('total_vaccinations')->nullable();;
            $table->float('taux_paiement')->nullable();;
            $table->float('taux_rdv_annules')->nullable();;
            $table->enum('status', ['active', 'inactive'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistiqueregionales');
    }
};
