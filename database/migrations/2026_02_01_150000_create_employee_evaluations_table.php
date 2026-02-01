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
        Schema::create('employee_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluated_by')->constrained('users')->onDelete('cascade');
            
            // Période d'évaluation
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('year');
            
            // Critères d'évaluation (sur les maximums définis)
            $table->decimal('problem_solving', 3, 1)->default(0); // /2 - Capacité à résoudre les problèmes
            $table->decimal('objectives_respect', 3, 1)->default(0); // /0.5 - Respect des objectifs fixés
            $table->decimal('work_under_pressure', 3, 1)->default(0); // /1 - Capacité à travailler sous pression
            $table->decimal('accountability', 3, 1)->default(0); // /2 - Capacité à rendre compte
            
            // Score total calculé (max 5.5)
            $table->decimal('total_score', 3, 1)->default(0);
            
            // Salaire calculé basé sur l'évaluation
            $table->decimal('calculated_salary', 12, 2)->default(0);
            
            // Commentaires
            $table->text('comments')->nullable();
            
            // Statut
            $table->enum('status', ['draft', 'validated'])->default('draft');
            $table->timestamp('validated_at')->nullable();
            
            $table->timestamps();
            
            // Un seul enregistrement par employé par mois
            $table->unique(['user_id', 'month', 'year']);
        });
        
        // Ajouter le paramètre SMIC dans settings
        \App\Models\Setting::firstOrCreate(
            ['key' => 'smic_amount'],
            ['value' => '75000', 'type' => 'number', 'group' => 'payroll']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_evaluations');
        \App\Models\Setting::where('key', 'smic_amount')->delete();
    }
};
