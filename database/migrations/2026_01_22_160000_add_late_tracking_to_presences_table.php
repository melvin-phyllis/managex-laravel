<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            // Retard à l'arrivée
            $table->boolean('is_late')->default(false)->after('check_out_status');
            $table->integer('late_minutes')->nullable()->after('is_late');

            // Départ anticipé
            $table->boolean('is_early_departure')->default(false)->after('late_minutes');
            $table->integer('early_departure_minutes')->nullable()->after('is_early_departure');

            // Type de départ (normal ou urgence)
            $table->enum('departure_type', ['normal', 'urgence'])->nullable()->after('early_departure_minutes');

            // Raison du départ d'urgence
            $table->text('early_departure_reason')->nullable()->after('departure_type');

            // Heures prévues pour ce jour (snapshot des settings au moment du pointage)
            $table->time('scheduled_start')->nullable()->after('early_departure_reason');
            $table->time('scheduled_end')->nullable()->after('scheduled_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->dropColumn([
                'is_late',
                'late_minutes',
                'is_early_departure',
                'early_departure_minutes',
                'departure_type',
                'early_departure_reason',
                'scheduled_start',
                'scheduled_end',
            ]);
        });
    }
};
