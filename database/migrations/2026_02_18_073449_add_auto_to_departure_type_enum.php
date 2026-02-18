<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE presences MODIFY COLUMN departure_type ENUM('normal', 'urgence', 'auto', 'recovery') NULL DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE presences MODIFY COLUMN departure_type ENUM('normal', 'urgence') NULL DEFAULT NULL");
    }
};
