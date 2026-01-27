<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Référence pays (nullable pour rétrocompatibilité)
            if (!Schema::hasColumn('payrolls', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('id')->constrained('payroll_countries')->nullOnDelete();
            }
            
            // Statut workflow
            if (!Schema::hasColumn('payrolls', 'workflow_status')) {
                $table->enum('workflow_status', ['draft', 'pending_review', 'validated', 'rejected'])->default('draft')->after('statut');
            }
            
            // Champs personnalisés
            if (!Schema::hasColumn('payrolls', 'custom_fields')) {
                $table->json('custom_fields')->nullable()->after('notes');
            }
            
            // Validation
            if (!Schema::hasColumn('payrolls', 'validated_at')) {
                $table->timestamp('validated_at')->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'validated_by')) {
                $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            }
            
            // Template utilisé (optionnel, sans contrainte si table n'existe pas)
            if (!Schema::hasColumn('payrolls', 'template_id')) {
                if (Schema::hasTable('payroll_templates')) {
                    $table->foreignId('template_id')->nullable()->constrained('payroll_templates')->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('template_id')->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['validated_by']);
            $table->dropForeign(['template_id']);
            $table->dropColumn([
                'country_id',
                'workflow_status',
                'custom_fields',
                'validated_at',
                'validated_by',
                'template_id'
            ]);
        });
    }
};
