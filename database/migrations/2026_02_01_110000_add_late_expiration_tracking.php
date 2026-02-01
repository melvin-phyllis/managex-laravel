<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Ajoute le suivi de l'expiration des heures de retard.
     * - late_recovery_deadline: date limite pour rattraper (7 jours après le retard)
     * - is_late_expired: si le retard a expiré sans être rattrapé
     * - expired_late_minutes: minutes de retard non rattrapées qui ont expiré
     */
    public function up(): void
    {
        // Champs d'expiration sur les présences
        Schema::table('presences', function (Blueprint $table) {
            // Date limite pour rattraper ce retard
            $table->date('late_recovery_deadline')->nullable()->after('recovery_minutes');
            
            // Le retard a expiré (non rattrapé dans le délai)
            $table->boolean('is_late_expired')->default(false)->after('late_recovery_deadline');
            
            // Minutes de retard qui ont expiré (late_minutes - recovery_minutes au moment de l'expiration)
            $table->unsignedInteger('expired_late_minutes')->default(0)->after('is_late_expired');
            
            // Index pour les requêtes d'expiration
            $table->index(['user_id', 'is_late_expired']);
            $table->index(['late_recovery_deadline', 'is_late']);
        });

        // Table pour enregistrer les absences générées par les retards expirés
        Schema::create('late_penalty_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('absence_date'); // Date de l'absence pénalité
            $table->unsignedInteger('total_expired_minutes'); // Total des minutes expirées qui ont déclenché l'absence
            $table->json('source_presence_ids'); // IDs des présences concernées
            $table->text('reason')->nullable();
            $table->boolean('is_acknowledged')->default(false); // L'employé a pris connaissance
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'absence_date']);
        });

        // Ajouter les settings par défaut
        \App\Models\Setting::set(
            'late_recovery_days',
            7,
            'integer',
            'presence',
            'Nombre de jours pour rattraper les heures de retard'
        );

        \App\Models\Setting::set(
            'late_penalty_threshold_minutes',
            480, // 8 heures par défaut
            'integer',
            'presence',
            'Seuil en minutes de retard expiré pour déclencher une absence pénalité'
        );

        \App\Models\Setting::set(
            'late_penalty_enabled',
            true,
            'boolean',
            'presence',
            'Activer le système de pénalité pour les retards non rattrapés'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('late_penalty_absences');

        Schema::table('presences', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_late_expired']);
            $table->dropIndex(['late_recovery_deadline', 'is_late']);
            $table->dropColumn(['late_recovery_deadline', 'is_late_expired', 'expired_late_minutes']);
        });

        \App\Models\Setting::where('key', 'late_recovery_days')->delete();
        \App\Models\Setting::where('key', 'late_penalty_threshold_minutes')->delete();
        \App\Models\Setting::where('key', 'late_penalty_enabled')->delete();
    }
};
