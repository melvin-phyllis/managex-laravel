<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'enum pour ajouter 'validated'
        DB::statement("ALTER TABLE tasks MODIFY COLUMN statut ENUM('pending', 'approved', 'rejected', 'completed', 'validated') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre l'ancien enum
        DB::statement("ALTER TABLE tasks MODIFY COLUMN statut ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending'");
    }
};
