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
        // SQLite doesn't support ENUM or MODIFY COLUMN, skip for SQLite
        // (SQLite stores ENUM as TEXT, so 'validated' values will work)
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN statut ENUM('pending', 'approved', 'rejected', 'completed', 'validated') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN statut ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending'");
        }
    }
};
