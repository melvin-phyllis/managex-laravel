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
        Schema::create('intern_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained('users')->cascadeOnDelete();
            $table->date('week_start'); // Lundi de la semaine évaluée

            // Scores (0-2.5 pour chaque critère, total sur 10)
            $table->decimal('discipline_score', 3, 1)->default(0);
            $table->decimal('behavior_score', 3, 1)->default(0);
            $table->decimal('skills_score', 3, 1)->default(0);
            $table->decimal('communication_score', 3, 1)->default(0);

            // Commentaires par critère
            $table->text('discipline_comment')->nullable();
            $table->text('behavior_comment')->nullable();
            $table->text('skills_comment')->nullable();
            $table->text('communication_comment')->nullable();

            // Commentaires généraux
            $table->text('general_comment')->nullable();
            $table->text('objectives_next_week')->nullable();

            // Statut
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            // Un seul enregistrement par stagiaire par semaine
            $table->unique(['intern_id', 'week_start']);

            // Index pour les requêtes fréquentes
            $table->index(['tutor_id', 'week_start']);
            $table->index(['status', 'week_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_evaluations');
    }
};
