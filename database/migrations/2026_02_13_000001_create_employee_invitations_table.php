<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();

            // Admin-provided fields
            $table->string('email')->index();
            $table->string('name');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('position_id')->nullable()->constrained('positions');
            $table->string('poste');
            $table->enum('contract_type', ['cdi', 'cdd', 'stage', 'alternance', 'freelance', 'interim'])->default('cdi');
            $table->date('hire_date');
            $table->date('contract_end_date')->nullable();
            $table->json('work_days');
            $table->decimal('base_salary', 10, 2)->nullable();

            // Invitation metadata
            $table->foreignId('invited_by')->constrained('users');
            $table->timestamp('expires_at');
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_invitations');
    }
};
