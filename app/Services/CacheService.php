<?php

namespace App\Services;

use App\Models\Leave;
use App\Models\Presence;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Service centralisé pour la gestion du cache
 * Optimise les performances pour 500+ utilisateurs simultanés
 */
class CacheService
{
    /**
     * TTL par défaut (en secondes)
     */
    const TTL_SHORT = 300;      // 5 minutes - données volatiles
    const TTL_MEDIUM = 900;     // 15 minutes - statistiques
    const TTL_LONG = 3600;      // 1 heure - données stables
    const TTL_VERY_LONG = 86400; // 24 heures - données rarement modifiées

    /**
     * Préfixes de cache
     */
    const PREFIX_DASHBOARD = 'dashboard.';
    const PREFIX_STATS = 'stats.';
    const PREFIX_USER = 'user.';

    /**
     * Statistiques du dashboard admin (cachées 5 minutes)
     */
    public static function getAdminDashboardStats(): array
    {
        return Cache::remember(self::PREFIX_DASHBOARD . 'admin.stats', self::TTL_SHORT, function () {
            $today = now()->toDateString();

            return [
                'total_employees' => User::where('role', 'employee')->count(),
                'active_employees' => User::where('role', 'employee')->where('status', 'active')->count(),
                'present_today' => Presence::whereDate('date', $today)
                    ->whereHas('user', fn($q) => $q->where('role', 'employee'))
                    ->distinct('user_id')
                    ->count('user_id'),
                'on_leave_today' => Leave::where('statut', 'approved')
                    ->whereDate('date_debut', '<=', $today)
                    ->whereDate('date_fin', '>=', $today)
                    ->distinct('user_id')
                    ->count('user_id'),
                'pending_leaves' => Leave::where('statut', 'pending')->count(),
                'pending_tasks' => Task::where('statut', 'pending')->count(),
                'overdue_tasks' => Task::where('date_fin', '<', now())
                    ->whereNotIn('statut', ['validated', 'completed'])
                    ->count(),
            ];
        });
    }

    /**
     * Statistiques des congés (cachées 5 minutes)
     */
    public static function getLeaveStats(): object
    {
        return Cache::remember(self::PREFIX_STATS . 'leaves', self::TTL_SHORT, function () {
            return Leave::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN statut = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN statut = 'approved' THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN statut = 'rejected' THEN 1 ELSE 0 END) as rejected_count
            ")->first();
        });
    }

    /**
     * Statistiques des tâches (cachées 5 minutes)
     */
    public static function getTaskStats(): object
    {
        return Cache::remember(self::PREFIX_STATS . 'tasks', self::TTL_SHORT, function () {
            return Task::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN statut = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN statut IN ('approved', 'in_progress') THEN 1 ELSE 0 END) as in_progress_count,
                SUM(CASE WHEN statut = 'completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN statut = 'validated' THEN 1 ELSE 0 END) as validated_count,
                SUM(CASE WHEN date_fin < NOW() AND statut NOT IN ('validated', 'completed') THEN 1 ELSE 0 END) as overdue_count
            ")->first();
        });
    }

    /**
     * Comptage des employés (caché 15 minutes)
     */
    public static function getEmployeeCount(): int
    {
        return Cache::remember(self::PREFIX_STATS . 'employee_count', self::TTL_MEDIUM, function () {
            return User::where('role', 'employee')->count();
        });
    }

    /**
     * Statistiques utilisateur spécifiques (cachées 5 minutes)
     */
    public static function getUserDashboardStats(int $userId): array
    {
        return Cache::remember(self::PREFIX_USER . $userId . '.dashboard', self::TTL_SHORT, function () use ($userId) {
            $today = now()->toDateString();
            $month = now()->month;
            $year = now()->year;

            return [
                'tasks_pending' => Task::where('user_id', $userId)->where('statut', 'pending')->count(),
                'tasks_in_progress' => Task::where('user_id', $userId)->whereIn('statut', ['approved', 'in_progress'])->count(),
                'tasks_completed' => Task::where('user_id', $userId)->where('statut', 'completed')->count(),
                'presences_month' => Presence::where('user_id', $userId)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->count(),
                'leaves_pending' => Leave::where('user_id', $userId)->where('statut', 'pending')->count(),
            ];
        });
    }

    /**
     * Invalider le cache du dashboard admin
     */
    public static function clearAdminDashboard(): void
    {
        Cache::forget(self::PREFIX_DASHBOARD . 'admin.stats');
        Cache::forget(self::PREFIX_STATS . 'leaves');
        Cache::forget(self::PREFIX_STATS . 'tasks');
        Cache::forget(self::PREFIX_STATS . 'employee_count');
    }

    /**
     * Invalider le cache utilisateur
     */
    public static function clearUserCache(int $userId): void
    {
        Cache::forget(self::PREFIX_USER . $userId . '.dashboard');
    }

    /**
     * Invalider tout le cache lié aux congés
     */
    public static function clearLeaveCache(): void
    {
        Cache::forget(self::PREFIX_STATS . 'leaves');
        Cache::forget(self::PREFIX_DASHBOARD . 'admin.stats');
    }

    /**
     * Invalider tout le cache lié aux tâches
     */
    public static function clearTaskCache(): void
    {
        Cache::forget(self::PREFIX_STATS . 'tasks');
        Cache::forget(self::PREFIX_DASHBOARD . 'admin.stats');
    }

    /**
     * Invalider tout le cache lié aux présences
     */
    public static function clearPresenceCache(): void
    {
        Cache::forget(self::PREFIX_DASHBOARD . 'admin.stats');
    }

    /**
     * Invalider tout le cache (à utiliser avec précaution)
     */
    public static function clearAll(): void
    {
        // Note: Cache::flush() efface TOUT le cache (y compris sessions si utilisant le même driver)
        // Préférer invalider les clés spécifiques
        self::clearAdminDashboard();
    }
}
