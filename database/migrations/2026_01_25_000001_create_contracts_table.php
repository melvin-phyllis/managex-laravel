<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('contract_type', ['cdi', 'cdd', 'stage', 'alternance', 'freelance', 'interim'])->default('cdi');
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_current']);
            $table->index('start_date');
        });

        // Transfer existing user contract data to contracts table
        $this->migrateUserContractData();
    }

    private function migrateUserContractData(): void
    {
        $users = DB::table('users')
            ->where(function ($query) {
                $query->whereNotNull('base_salary')
                    ->orWhereNotNull('hire_date');
            })
            ->get();

        foreach ($users as $user) {
            DB::table('contracts')->insert([
                'user_id' => $user->id,
                'contract_type' => $user->contract_type ?? 'cdi',
                'base_salary' => $user->base_salary ?? 0,
                'start_date' => $user->hire_date ?? now()->toDateString(),
                'end_date' => $user->contract_end_date,
                'is_current' => true,
                'notes' => 'Migre automatiquement depuis la table users',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
