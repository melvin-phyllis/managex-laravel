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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, time, json
            $table->string('group')->nullable(); // presence, leaves, payroll, general
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insérer les paramètres par défaut
        DB::table('settings')->insert([
            [
                'key' => 'work_start_time',
                'value' => '08:00',
                'type' => 'time',
                'group' => 'presence',
                'description' => 'Heure de début de travail',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'work_end_time',
                'value' => '17:00',
                'type' => 'time',
                'group' => 'presence',
                'description' => 'Heure de fin de travail',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'break_start_time',
                'value' => '12:00',
                'type' => 'time',
                'group' => 'presence',
                'description' => 'Heure de début de pause déjeuner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'break_end_time',
                'value' => '13:00',
                'type' => 'time',
                'group' => 'presence',
                'description' => 'Heure de fin de pause déjeuner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'late_tolerance_minutes',
                'value' => '15',
                'type' => 'integer',
                'group' => 'presence',
                'description' => 'Tolérance de retard en minutes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
