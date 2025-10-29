<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. DÉFINITION DU TYPE ENUM POSTGRESQL
        if (DB::getDriverName() === 'pgsql') {
            // Créer le type ENUM si on est sur PostgreSQL
            // La clause 'IF NOT EXISTS' est facultative mais prévient les erreurs
            DB::statement("
                DO $$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'appointment_status_type') THEN
                        CREATE TYPE appointment_status_type AS ENUM ('pending', 'confirmed', 'canceled', 'rescheduled', 'completed');
                    END IF;
                END
                $$;
            ");
        }

        // Vérifier que la table appointments existe
        if (!Schema::hasTable('appointments')) {
            // Si la table n'existe pas, on ne peut pas la modifier/reconstruire.
            return;
        }

        // Récupérer la structure actuelle (méthode de la table temporaire conservée pour l'exemple)
        $columns = Schema::getColumnListing('appointments');

        // Créer une nouvelle table temporaire
        Schema::create('appointments_temp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->dateTime('date')->nullable();

            if (DB::getDriverName() === 'pgsql') {
                // Utiliser le type ENUM créé manuellement pour PostgreSQL
                $table->addColumn('appointment_status_type', 'status')->default('pending');
            } else {
                // Utiliser la fonction enum() native de Laravel pour les autres DB (MySQL, etc.)
                $table->enum('status', ['pending', 'confirmed', 'canceled', 'rescheduled', 'completed'])->default('pending');
            }

            $table->timestamps();
        });

        // Copier les données
        $existingColumns = collect(['id', 'patient_id', 'doctor_id', 'date', 'status', 'created_at', 'updated_at'])
            ->filter(fn($col) => in_array($col, $columns))
            ->implode(', ');

        if ($existingColumns) {
            DB::statement("INSERT INTO appointments_temp ($existingColumns) SELECT $existingColumns FROM appointments");
        }

        // Supprimer l'ancienne table
        Schema::drop('appointments');

        // Renommer la nouvelle table
        Schema::rename('appointments_temp', 'appointments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Logique de 'down' pour revenir à une simple chaîne de caractères
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('status', 255)->default('pending')->change();
        });

        // Suppression du type ENUM PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP TYPE IF EXISTS appointment_status_type;');
        }
    }
};
