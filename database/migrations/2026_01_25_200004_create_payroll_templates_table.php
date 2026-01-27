<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('payroll_countries')->onDelete('cascade');
            
            $table->string('name');
            $table->string('blade_path'); // pdf.payroll-civ, pdf.payroll-fra...
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_templates');
    }
};
