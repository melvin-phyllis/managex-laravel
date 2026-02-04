<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour ajouter les index de performance critiques
 * Optimisation pour 500+ utilisateurs simultanés
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // =====================================================
        // TABLE: users - Index pour filtrage et recherche
        // =====================================================
        Schema::table('users', function (Blueprint $table) {
            // Index sur role (filtrage admin/employee)
            $table->index('role', 'idx_users_role');

            // Index sur status (filtrage active/terminated)
            $table->index('status', 'idx_users_status');

            // Index composé pour recherche d'employés actifs par département
            $table->index(['department_id', 'status'], 'idx_users_dept_status');

            // Index composé pour recherche d'employés actifs par rôle
            $table->index(['role', 'status'], 'idx_users_role_status');
        });

        // =====================================================
        // TABLE: presences - Index pour requêtes date/statut
        // =====================================================
        Schema::table('presences', function (Blueprint $table) {
            // Index sur date (requêtes par plage de dates)
            $table->index('date', 'idx_presences_date');

            // Index composé user + date (très fréquent)
            $table->index(['user_id', 'date'], 'idx_presences_user_date');

            // Index sur is_late (filtrage retards)
            $table->index('is_late', 'idx_presences_is_late');

            // Index composé pour statistiques mensuelles
            $table->index(['user_id', 'is_late', 'date'], 'idx_presences_user_late_date');

            // Index sur check_in (requêtes horaires)
            $table->index('check_in', 'idx_presences_check_in');
        });

        // =====================================================
        // TABLE: tasks - Index pour filtrage et tri
        // =====================================================
        Schema::table('tasks', function (Blueprint $table) {
            // Index sur statut (filtrage par état)
            $table->index('statut', 'idx_tasks_statut');

            // Index sur priorité (tri par priorité)
            $table->index('priorite', 'idx_tasks_priorite');

            // Index composé user + statut (mes tâches par statut)
            $table->index(['user_id', 'statut'], 'idx_tasks_user_statut');

            // Index sur date_fin (tâches en retard)
            $table->index('date_fin', 'idx_tasks_date_fin');

            // Index composé pour dashboard (tâches actives d'un user)
            $table->index(['user_id', 'statut', 'date_fin'], 'idx_tasks_user_statut_datefin');
        });

        // =====================================================
        // TABLE: leaves - Index pour filtrage congés
        // =====================================================
        Schema::table('leaves', function (Blueprint $table) {
            // Index sur statut (pending/approved/rejected)
            $table->index('statut', 'idx_leaves_statut');

            // Index sur type (congé payé, maladie, etc.)
            $table->index('type', 'idx_leaves_type');

            // Index composé user + statut
            $table->index(['user_id', 'statut'], 'idx_leaves_user_statut');

            // Index sur dates (plages de congés)
            $table->index('date_debut', 'idx_leaves_date_debut');
            $table->index('date_fin', 'idx_leaves_date_fin');

            // Index composé pour vérifier congés actifs
            $table->index(['statut', 'date_debut', 'date_fin'], 'idx_leaves_statut_dates');
        });

        // =====================================================
        // TABLE: payrolls - Index pour recherche paie
        // =====================================================
        Schema::table('payrolls', function (Blueprint $table) {
            // Index sur statut
            $table->index('statut', 'idx_payrolls_statut');

            // Index composé année + mois (recherche par période)
            $table->index(['annee', 'mois'], 'idx_payrolls_annee_mois');

            // Index composé user + année + mois
            $table->index(['user_id', 'annee', 'mois'], 'idx_payrolls_user_periode');
        });

        // =====================================================
        // TABLE: notifications - Index pour non-lues
        // =====================================================
        Schema::table('notifications', function (Blueprint $table) {
            // Index sur read_at (filtrer non-lues)
            $table->index('read_at', 'idx_notifications_read_at');

            // Index sur type (filtrer par type)
            $table->index('type', 'idx_notifications_type');
        });

        // =====================================================
        // TABLE: surveys - Index pour sondages actifs
        // =====================================================
        Schema::table('surveys', function (Blueprint $table) {
            // Index sur is_active
            $table->index('is_active', 'idx_surveys_is_active');

            // Index sur date_limite
            $table->index('date_limite', 'idx_surveys_date_limite');
        });

        // =====================================================
        // TABLE: contracts - Index pour contrats actifs
        // =====================================================
        Schema::table('contracts', function (Blueprint $table) {
            // Index sur end_date (contrats expirant)
            $table->index('end_date', 'idx_contracts_end_date');

            // Index composé user + is_current
            $table->index(['user_id', 'is_current'], 'idx_contracts_user_current');
        });

        // =====================================================
        // TABLE: employee_evaluations - Index pour évaluations
        // =====================================================
        Schema::table('employee_evaluations', function (Blueprint $table) {
            // Index sur période
            $table->index(['month', 'year'], 'idx_employee_eval_periode');

            // Index sur statut
            $table->index('status', 'idx_employee_eval_status');
        });

        // =====================================================
        // TABLE: conversation_participants - Index last_read_at
        // =====================================================
        Schema::table('conversation_participants', function (Blueprint $table) {
            // Index sur last_read_at (calcul messages non lus)
            $table->index('last_read_at', 'idx_conv_participants_last_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_status');
            $table->dropIndex('idx_users_dept_status');
            $table->dropIndex('idx_users_role_status');
        });

        // Presences
        Schema::table('presences', function (Blueprint $table) {
            $table->dropIndex('idx_presences_date');
            $table->dropIndex('idx_presences_user_date');
            $table->dropIndex('idx_presences_is_late');
            $table->dropIndex('idx_presences_user_late_date');
            $table->dropIndex('idx_presences_check_in');
        });

        // Tasks
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_statut');
            $table->dropIndex('idx_tasks_priorite');
            $table->dropIndex('idx_tasks_user_statut');
            $table->dropIndex('idx_tasks_date_fin');
            $table->dropIndex('idx_tasks_user_statut_datefin');
        });

        // Leaves
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropIndex('idx_leaves_statut');
            $table->dropIndex('idx_leaves_type');
            $table->dropIndex('idx_leaves_user_statut');
            $table->dropIndex('idx_leaves_date_debut');
            $table->dropIndex('idx_leaves_date_fin');
            $table->dropIndex('idx_leaves_statut_dates');
        });

        // Payrolls
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropIndex('idx_payrolls_statut');
            $table->dropIndex('idx_payrolls_annee_mois');
            $table->dropIndex('idx_payrolls_user_periode');
        });

        // Notifications
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_read_at');
            $table->dropIndex('idx_notifications_type');
        });

        // Surveys
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropIndex('idx_surveys_is_active');
            $table->dropIndex('idx_surveys_date_limite');
        });

        // Contracts
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropIndex('idx_contracts_end_date');
            $table->dropIndex('idx_contracts_user_current');
        });

        // Employee Evaluations
        Schema::table('employee_evaluations', function (Blueprint $table) {
            $table->dropIndex('idx_employee_eval_periode');
            $table->dropIndex('idx_employee_eval_status');
        });

        // Conversation Participants
        Schema::table('conversation_participants', function (Blueprint $table) {
            $table->dropIndex('idx_conv_participants_last_read');
        });
    }
};
