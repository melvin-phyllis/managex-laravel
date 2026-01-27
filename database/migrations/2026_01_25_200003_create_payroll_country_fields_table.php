<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_country_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('payroll_countries')->onDelete('cascade');
            
            $table->string('field_name'); // transport_allowance, housing_allowance...
            $table->string('field_label'); // Libellé affiché
            $table->enum('field_type', ['text', 'number', 'select', 'date', 'boolean', 'textarea']);
            $table->json('options')->nullable(); // Options pour select [{"value":"cdi", "label":"CDI"}, ...]
            
            $table->boolean('is_required')->default(false);
            $table->string('default_value')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('help_text')->nullable();
            
            // Validation
            $table->decimal('min_value', 15, 2)->nullable();
            $table->decimal('max_value', 15, 2)->nullable();
            
            // Catégorisation
            $table->string('section')->default('allowances'); // earnings, allowances, deductions, info
            $table->unsignedInteger('display_order')->default(0);
            
            // Comportement
            $table->boolean('is_taxable')->default(true); // Ce champ est-il imposable?
            $table->boolean('affects_gross')->default(true); // Affecte le brut?
            
            $table->timestamps();
            
            $table->unique(['country_id', 'field_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_country_fields');
    }
};
