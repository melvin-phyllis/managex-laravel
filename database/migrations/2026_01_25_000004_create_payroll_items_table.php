<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['earning', 'deduction']);
            $table->string('code', 50)->nullable()->comment('Internal code for categorization');
            $table->string('label', 150);
            $table->decimal('amount', 12, 2);
            $table->decimal('base', 12, 2)->nullable()->comment('Base amount for percentage calculations');
            $table->decimal('rate', 5, 4)->nullable()->comment('Rate if applicable');
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_subject_to_cnps')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['payroll_id', 'type']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
