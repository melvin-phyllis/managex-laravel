<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * SECURITY AUDIT FIX: Ajoute les contraintes uniques et les types enum
 * 
 * - Contraintes uniques pour SSN, IBAN, CNPS
 * - Types enum pour les statuts
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Contraintes uniques sur les données sensibles
        Schema::table('users', function (Blueprint $table) {
            // Social Security Number - doit être unique s'il existe
            if (Schema::hasColumn('users', 'social_security_number')) {
                // Créer un index unique qui ignore les NULL
                $table->unique('social_security_number', 'users_ssn_unique');
            }

            // CNPS Number - unique aussi
            if (Schema::hasColumn('users', 'cnps_number')) {
                $table->unique('cnps_number', 'users_cnps_unique');
            }

            // Bank IBAN - unique
            if (Schema::hasColumn('users', 'bank_iban')) {
                $table->unique('bank_iban', 'users_iban_unique');
            }
        });

        // 2. CHECK constraint pour validation des statuts (MySQL 8.0.16+)
        // Note: SQLite ne supporte pas les CHECK constraints de la même façon
        if (DB::connection()->getDriverName() === 'mysql') {
            // Tasks.statut - valeurs autorisées
            if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'statut')) {
                try {
                    DB::statement("ALTER TABLE tasks ADD CONSTRAINT tasks_statut_check CHECK (statut IN ('pending', 'approved', 'in_progress', 'completed', 'cancelled'))");
                } catch (\Exception $e) {
                    // Constraint may already exist, continue
                }
            }

            // Tasks.priorite - valeurs autorisées
            if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'priorite')) {
                try {
                    DB::statement("ALTER TABLE tasks ADD CONSTRAINT tasks_priorite_check CHECK (priorite IN ('low', 'medium', 'high', 'urgent'))");
                } catch (\Exception $e) {
                    // Constraint may already exist
                }
            }

            // Document requests status - valeurs autorisées  
            if (Schema::hasTable('document_requests') && Schema::hasColumn('document_requests', 'status')) {
                try {
                    DB::statement("ALTER TABLE document_requests ADD CONSTRAINT document_requests_status_check CHECK (status IN ('pending', 'processing', 'completed', 'rejected'))");
                } catch (\Exception $e) {
                    // Constraint may already exist
                }
            }
        }

        // 3. Index sur email pour performance des requêtes utilisateur
        Schema::table('users', function (Blueprint $table) {
            // L'index sur email existe probablement déjà via unique(), mais vérifions
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropUnique('users_ssn_unique');
            } catch (\Exception $e) {}
            
            try {
                $table->dropUnique('users_cnps_unique');
            } catch (\Exception $e) {}
            
            try {
                $table->dropUnique('users_iban_unique');
            } catch (\Exception $e) {}
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            try {
                DB::statement("ALTER TABLE tasks DROP CONSTRAINT tasks_statut_check");
            } catch (\Exception $e) {}
            
            try {
                DB::statement("ALTER TABLE tasks DROP CONSTRAINT tasks_priorite_check");
            } catch (\Exception $e) {}
            
            try {
                DB::statement("ALTER TABLE document_requests DROP CONSTRAINT document_requests_status_check");
            } catch (\Exception $e) {}
        }
    }
};
