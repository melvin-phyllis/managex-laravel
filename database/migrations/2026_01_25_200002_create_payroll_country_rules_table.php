<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_country_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('payroll_countries')->onDelete('cascade');

            // Type de règle
            $table->enum('rule_type', ['tax', 'contribution', 'allowance', 'deduction', 'earning']);
            $table->enum('rule_category', ['employee', 'employer', 'both'])->default('employee');

            // Identification
            $table->string('code', 20); // IS, CN, IGR, CNPS...
            $table->string('label');
            $table->text('description')->nullable();

            // Mode de calcul
            $table->enum('calculation_type', ['percentage', 'fixed', 'bracket', 'formula']);
            $table->decimal('rate', 8, 4)->nullable(); // Taux si percentage (ex: 1.2 pour 1.2%)
            $table->decimal('fixed_amount', 15, 2)->nullable(); // Montant fixe
            $table->json('brackets')->nullable(); // Barèmes progressifs [{"min":0, "max":50000, "rate":0}, ...]
            $table->string('formula')->nullable(); // Formule personnalisée (ex: "taxable_gross * 0.012")

            // Base de calcul
            $table->string('base_field')->default('taxable_gross'); // gross_salary, taxable_gross, net_before_tax...
            $table->decimal('ceiling', 15, 2)->nullable(); // Plafond
            $table->decimal('floor', 15, 2)->nullable(); // Plancher

            // Flags
            $table->boolean('is_taxable')->default(false); // Cette retenue est-elle incluse dans le brut imposable?
            $table->boolean('is_deductible')->default(false); // Déductible de la base IGR?
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_visible_on_payslip')->default(true);

            // Affichage
            $table->unsignedInteger('display_order')->default(0);
            $table->string('pdf_code', 10)->nullable(); // Code affiché sur le PDF (30, 370, 565...)

            $table->timestamps();

            $table->unique(['country_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_country_rules');
    }
};
