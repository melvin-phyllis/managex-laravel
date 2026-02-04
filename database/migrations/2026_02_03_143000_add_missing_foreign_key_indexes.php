<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SECURITY AUDIT FIX: Ajoute les index manquants sur les clés étrangères
 * 
 * Ces index sont critiques pour les performances des JOIN.
 * Identifiés lors de l'audit de sécurité du 2026-02-03.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tasks
        if (Schema::hasColumn('tasks', 'user_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                if (!$this->hasIndex('tasks', 'tasks_user_id_index')) {
                    $table->index('user_id');
                }
            });
        }

        // Leaves
        if (Schema::hasColumn('leaves', 'user_id')) {
            Schema::table('leaves', function (Blueprint $table) {
                if (!$this->hasIndex('leaves', 'leaves_user_id_index')) {
                    $table->index('user_id');
                }
            });
        }

        // Payrolls
        if (Schema::hasColumn('payrolls', 'user_id')) {
            Schema::table('payrolls', function (Blueprint $table) {
                if (!$this->hasIndex('payrolls', 'payrolls_user_id_index')) {
                    $table->index('user_id');
                }
            });
        }

        // Surveys - uses admin_id, not user_id
        if (Schema::hasColumn('surveys', 'admin_id')) {
            Schema::table('surveys', function (Blueprint $table) {
                if (!$this->hasIndex('surveys', 'surveys_admin_id_index')) {
                    $table->index('admin_id');
                }
            });
        }

        // Survey Questions
        if (Schema::hasColumn('survey_questions', 'survey_id')) {
            Schema::table('survey_questions', function (Blueprint $table) {
                if (!$this->hasIndex('survey_questions', 'survey_questions_survey_id_index')) {
                    $table->index('survey_id');
                }
            });
        }

        // Survey Responses
        if (Schema::hasTable('survey_responses')) {
            Schema::table('survey_responses', function (Blueprint $table) {
                if (Schema::hasColumn('survey_responses', 'survey_question_id') && !$this->hasIndex('survey_responses', 'survey_responses_survey_question_id_index')) {
                    $table->index('survey_question_id');
                }
                if (Schema::hasColumn('survey_responses', 'user_id') && !$this->hasIndex('survey_responses', 'survey_responses_user_id_index')) {
                    $table->index('user_id');
                }
            });
        }

        // Positions
        if (Schema::hasColumn('positions', 'department_id')) {
            Schema::table('positions', function (Blueprint $table) {
                if (!$this->hasIndex('positions', 'positions_department_id_index')) {
                    $table->index('department_id');
                }
            });
        }

        // Contracts
        if (Schema::hasColumn('contracts', 'user_id')) {
            Schema::table('contracts', function (Blueprint $table) {
                if (!$this->hasIndex('contracts', 'contracts_user_id_index')) {
                    $table->index('user_id');
                }
            });
        }

        // Payroll Items
        if (Schema::hasColumn('payroll_items', 'payroll_id')) {
            Schema::table('payroll_items', function (Blueprint $table) {
                if (!$this->hasIndex('payroll_items', 'payroll_items_payroll_id_index')) {
                    $table->index('payroll_id');
                }
            });
        }

        // Payroll Country Rules
        if (Schema::hasTable('payroll_country_rules') && Schema::hasColumn('payroll_country_rules', 'country_id')) {
            Schema::table('payroll_country_rules', function (Blueprint $table) {
                if (!$this->hasIndex('payroll_country_rules', 'payroll_country_rules_country_id_index')) {
                    $table->index('country_id');
                }
            });
        }

        // Payroll Fields
        if (Schema::hasTable('payroll_fields') && Schema::hasColumn('payroll_fields', 'country_id')) {
            Schema::table('payroll_fields', function (Blueprint $table) {
                if (!$this->hasIndex('payroll_fields', 'payroll_fields_country_id_index')) {
                    $table->index('country_id');
                }
            });
        }

        // Payroll Templates
        if (Schema::hasTable('payroll_templates') && Schema::hasColumn('payroll_templates', 'country_id')) {
            Schema::table('payroll_templates', function (Blueprint $table) {
                if (!$this->hasIndex('payroll_templates', 'payroll_templates_country_id_index')) {
                    $table->index('country_id');
                }
            });
        }

        // Documents
        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (Schema::hasColumn('documents', 'user_id') && !$this->hasIndex('documents', 'documents_user_id_index')) {
                    $table->index('user_id');
                }
                if (Schema::hasColumn('documents', 'document_type_id') && !$this->hasIndex('documents', 'documents_document_type_id_index')) {
                    $table->index('document_type_id');
                }
                if (Schema::hasColumn('documents', 'validated_by') && !$this->hasIndex('documents', 'documents_validated_by_index')) {
                    $table->index('validated_by');
                }
                if (Schema::hasColumn('documents', 'uploaded_by') && !$this->hasIndex('documents', 'documents_uploaded_by_index')) {
                    $table->index('uploaded_by');
                }
            });
        }

        // Document Requests
        if (Schema::hasTable('document_requests') && Schema::hasColumn('document_requests', 'admin_id')) {
            Schema::table('document_requests', function (Blueprint $table) {
                if (!$this->hasIndex('document_requests', 'document_requests_admin_id_index')) {
                    $table->index('admin_id');
                }
            });
        }

        // Document Types
        if (Schema::hasTable('document_types') && Schema::hasColumn('document_types', 'category_id')) {
            Schema::table('document_types', function (Blueprint $table) {
                if (!$this->hasIndex('document_types', 'document_types_category_id_index')) {
                    $table->index('category_id');
                }
            });
        }

        // Intern Evaluations
        if (Schema::hasTable('intern_evaluations')) {
            Schema::table('intern_evaluations', function (Blueprint $table) {
                if (Schema::hasColumn('intern_evaluations', 'intern_id') && !$this->hasIndex('intern_evaluations', 'intern_evaluations_intern_id_index')) {
                    $table->index('intern_id');
                }
                if (Schema::hasColumn('intern_evaluations', 'tutor_id') && !$this->hasIndex('intern_evaluations', 'intern_evaluations_tutor_id_index')) {
                    $table->index('tutor_id');
                }
            });
        }

        // Employee Evaluations
        if (Schema::hasTable('employee_evaluations')) {
            Schema::table('employee_evaluations', function (Blueprint $table) {
                if (Schema::hasColumn('employee_evaluations', 'user_id') && !$this->hasIndex('employee_evaluations', 'employee_evaluations_user_id_index')) {
                    $table->index('user_id');
                }
                if (Schema::hasColumn('employee_evaluations', 'evaluated_by') && !$this->hasIndex('employee_evaluations', 'employee_evaluations_evaluated_by_index')) {
                    $table->index('evaluated_by');
                }
            });
        }

        // Late Penalty Absences
        if (Schema::hasTable('late_penalty_absences') && Schema::hasColumn('late_penalty_absences', 'user_id')) {
            Schema::table('late_penalty_absences', function (Blueprint $table) {
                if (!$this->hasIndex('late_penalty_absences', 'late_penalty_absences_user_id_index')) {
                    $table->index('user_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexes = [
            'tasks' => ['user_id'],
            'leaves' => ['user_id'],
            'payrolls' => ['user_id'],
            'surveys' => ['admin_id'],
            'survey_questions' => ['survey_id'],
            'survey_responses' => ['survey_question_id', 'user_id'],
            'positions' => ['department_id'],
            'contracts' => ['user_id'],
            'payroll_items' => ['payroll_id'],
        ];

        foreach ($indexes as $table => $columns) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($columns) {
                    foreach ($columns as $column) {
                        try {
                            $table->dropIndex([$column]);
                        } catch (\Exception $e) {
                            // Index may not exist, continue
                        }
                    }
                });
            }
        }
    }

    /**
     * Check if an index exists on a table.
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if ($index['name'] === $indexName) {
                return true;
            }
        }
        return false;
    }
};
