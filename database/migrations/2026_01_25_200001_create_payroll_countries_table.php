<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // ISO 3166-1 alpha-3 (CIV, FRA, SEN...)
            $table->string('name');
            $table->string('currency', 3)->default('XOF'); // ISO 4217
            $table->string('currency_symbol', 10)->default('FCFA');
            $table->json('legal_mentions')->nullable(); // Mentions légales obligatoires
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_countries');
    }
};
