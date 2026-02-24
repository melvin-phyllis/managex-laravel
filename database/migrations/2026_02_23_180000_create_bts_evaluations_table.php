<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bts_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();

            // Intern BTS info
            $table->string('intern_bts_number')->nullable();
            $table->string('intern_field')->nullable(); // Filière
            $table->date('stage_start_date');
            $table->date('stage_end_date');

            // ===== 5 CRITÈRES BTS (total /20) =====

            // 1. Assiduité et ponctualité (/3) — AUTO depuis présences
            $table->decimal('assiduity_score', 3, 1)->default(0);
            $table->text('assiduity_details')->nullable(); // Détails calcul auto

            // 2. Relations humaines et professionnelles (/4) — MANUELLE (sous-critères)
            $table->decimal('relations_score', 3, 1)->default(0);
            $table->boolean('relations_teamwork')->default(false);      // S'intègre dans l'équipe
            $table->boolean('relations_hierarchy')->default(false);     // Respecte la hiérarchie
            $table->boolean('relations_courtesy')->default(false);      // Courtoisie / savoir-être
            $table->boolean('relations_listening')->default(false);     // Écoute et accepte les remarques

            // 3. Intelligence d'exécution des tâches (/6) — AUTO depuis tâches
            $table->decimal('execution_score', 3, 1)->default(0);
            $table->text('execution_details')->nullable(); // Détails calcul auto

            // 4. Esprit d'initiative (/4) — SEMI-AUTO
            $table->decimal('initiative_score', 3, 1)->default(0);
            $table->text('initiative_details')->nullable();

            // 5. Présentation (/3) — MANUELLE (sous-critères)
            $table->decimal('presentation_score', 3, 1)->default(0);
            $table->boolean('presentation_dress')->default(false);      // Tenue vestimentaire correcte
            $table->boolean('presentation_oral')->default(false);       // Expression orale claire
            $table->boolean('presentation_attitude')->default(false);   // Attitude professionnelle

            // Total calculé
            $table->decimal('total_score', 4, 1)->default(0);

            // Appréciation du maître de stage
            $table->text('appreciation')->nullable();

            // Rapport justificatif (obligatoire si > 16/20)
            $table->text('justification_report')->nullable();

            // Statut
            $table->enum('status', ['draft', 'submitted', 'signed'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('signed_at')->nullable();

            $table->timestamps();

            // Un stagiaire = une seule fiche BTS par période
            $table->unique(['intern_id', 'stage_start_date', 'stage_end_date'], 'bts_eval_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bts_evaluations');
    }
};
