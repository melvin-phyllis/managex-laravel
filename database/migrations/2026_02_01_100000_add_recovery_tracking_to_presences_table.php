<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Ajoute le suivi du rattrapage des heures de retard.
     * - overtime_minutes: minutes travaillées au-delà de l'horaire prévu
     * - recovery_minutes: minutes comptées comme rattrapage
     * - is_recovery_session: si l'employé a indiqué que c'est une session de rattrapage
     */
    public function up(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            // Minutes supplémentaires travaillées (calculé automatiquement au check-out)
            $table->unsignedInteger('overtime_minutes')->default(0)->after('late_minutes');
            
            // Minutes de rattrapage (portion des heures sup utilisée pour rattraper)
            $table->unsignedInteger('recovery_minutes')->default(0)->after('overtime_minutes');
            
            // L'employé a marqué cette journée comme session de rattrapage
            $table->boolean('is_recovery_session')->default(false)->after('recovery_minutes');
            
            // Index pour les requêtes de balance
            $table->index(['user_id', 'is_late']);
            $table->index(['user_id', 'recovery_minutes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_late']);
            $table->dropIndex(['user_id', 'recovery_minutes']);
            $table->dropColumn(['overtime_minutes', 'recovery_minutes', 'is_recovery_session']);
        });
    }
};
