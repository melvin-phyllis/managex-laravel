<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\GeolocationZone;
use App\Models\Presence;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = $user->presences();

        if ($request->filled('mois')) {
            $date = Carbon::parse($request->mois);
            $query->month($date->month, $date->year);
        }

        $presences = $query->orderBy('date', 'desc')->paginate(15);
        $todayPresence = $user->todayPresence();

        // Statistiques du mois courant
        $currentMonth = now();
        $monthlyPresences = $user->presences()
            ->month($currentMonth->month, $currentMonth->year)
            ->get();

        // Calculer le score de ponctualité
        $totalDaysPresent = $monthlyPresences->count();
        $daysOnTime = $monthlyPresences->where('is_late', false)->count();
        $punctualityScore = $totalDaysPresent > 0 ? round(($daysOnTime / $totalDaysPresent) * 100) : 100;

        // Calculer les heures supplémentaires (au-delà de 8h par jour)
        $dailyTarget = 8; // heures par jour
        $overtimeHours = 0;
        foreach ($monthlyPresences as $p) {
            if ($p->hours_worked && $p->hours_worked > $dailyTarget) {
                $overtimeHours += ($p->hours_worked - $dailyTarget);
            }
        }

        // Objectif mensuel (jours ouvrables * 8h)
        $workDays = $user->getWorkDayNumbers();
        $workingDaysInMonth = 0;
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        for ($date = $startOfMonth; $date <= $endOfMonth; $date->addDay()) {
            if (in_array($date->dayOfWeekIso, $workDays)) {
                $workingDaysInMonth++;
            }
        }
        $monthlyTargetHours = $workingDaysInMonth * $dailyTarget;

        // Calculer les statistiques de rattrapage
        $monthlyRecoveryMinutes = $monthlyPresences->sum('recovery_minutes');
        $monthlyOvertimeMinutes = $monthlyPresences->sum('overtime_minutes');

        $monthlyStats = [
            'days_present' => $monthlyPresences->count(),
            'total_hours' => $monthlyPresences->sum('hours_worked'),
            'average_hours' => $monthlyPresences->count() > 0
                ? $monthlyPresences->avg('hours_worked')
                : 0,
            'total_late' => $monthlyPresences->where('is_late', true)->count(),
            'total_late_minutes' => $monthlyPresences->sum('late_minutes'),
            'punctuality_score' => $punctualityScore,
            'overtime_hours' => round($overtimeHours, 1),
            'target_hours' => $monthlyTargetHours,
            'working_days_in_month' => $workingDaysInMonth,
            // Recovery stats
            'monthly_overtime_minutes' => $monthlyOvertimeMinutes,
            'monthly_recovery_minutes' => $monthlyRecoveryMinutes,
        ];

        // Statistiques globales de rattrapage
        $recoveryStats = $user->recovery_stats;

        // Données d'expiration des retards
        $expiringLateData = [
            'expiring_presences' => $user->getExpiringLatePresences(),
            'expiring_minutes' => $user->expiring_late_minutes,
            'expired_minutes' => $user->expired_late_minutes,
            'penalty_threshold' => Setting::getLatePenaltyThresholdMinutes(),
            'upcoming_penalties' => $user->upcoming_penalty_absences,
            'recovery_days' => Setting::getLateRecoveryDays(),
        ];

        // Liste des retards à rattraper (non expirés, avec solde > 0)
        // Limiter aux 30 derniers jours pour éviter une liste trop longue
        $lateToRecoverQuery = $user->presences()
            ->where('is_late', true)
            ->where('is_late_expired', false)
            ->whereRaw('late_minutes > recovery_minutes')
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('late_recovery_deadline');

        $lateToRecoverCount = $lateToRecoverQuery->count();

        $lateToRecover = $lateToRecoverQuery
            ->limit(5) // Afficher seulement les 5 plus urgents
            ->get()
            ->map(function ($presence) {
                return [
                    'id' => $presence->id,
                    'date' => $presence->date,
                    'late_minutes' => $presence->late_minutes,
                    'recovery_minutes' => $presence->recovery_minutes,
                    'unrecovered_minutes' => $presence->unrecovered_minutes,
                    'deadline' => $presence->late_recovery_deadline,
                    'days_remaining' => $presence->days_to_recover,
                    'status' => $presence->recovery_status,
                ];
            });

        // Total des minutes à rattraper (tous les retards, pas seulement les 5 affichés)
        // Utiliser GREATEST pour éviter les valeurs négatives (UNSIGNED)
        $totalUnrecoveredMinutes = $user->presences()
            ->where('is_late', true)
            ->where('is_late_expired', false)
            ->selectRaw('SUM(GREATEST(0, CAST(late_minutes AS SIGNED) - CAST(recovery_minutes AS SIGNED))) as total')
            ->value('total') ?? 0;

        // Retards expirés (pour historique) - limiter à 5
        $expiredLate = $user->presences()
            ->where('is_late_expired', true)
            ->where('expired_late_minutes', '>', 0)
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        // Données pour le graphique hebdomadaire (7 derniers jours)
        $weeklyData = [];
        $weeklyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyLabels[] = $date->translatedFormat('D d');

            $presence = $user->presences()
                ->whereDate('date', $date->toDateString())
                ->first();

            $weeklyData[] = $presence ? round($presence->hours_worked ?? 0, 1) : 0;
        }

        // Données pour le calendrier mensuel
        $calendarData = [];
        $monthStart = $currentMonth->copy()->startOfMonth();
        $monthEnd = $currentMonth->copy()->endOfMonth();

        // Récupérer toutes les présences du mois
        $monthPresences = $user->presences()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get()
            ->keyBy(fn ($p) => $p->date->format('Y-m-d'));

        // Récupérer les congés du mois
        $monthLeaves = \App\Models\Leave::where('user_id', $user->id)
            ->where('statut', 'approved')
            ->where(function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('date_debut', [$monthStart, $monthEnd])
                    ->orWhereBetween('date_fin', [$monthStart, $monthEnd])
                    ->orWhere(function ($q2) use ($monthStart, $monthEnd) {
                        $q2->where('date_debut', '<=', $monthStart)
                            ->where('date_fin', '>=', $monthEnd);
                    });
            })
            ->get();

        // Créer un tableau des jours en congé
        $leaveDays = [];
        foreach ($monthLeaves as $leave) {
            $start = $leave->date_debut->copy();
            $end = $leave->date_fin->copy();
            // Limiter aux bornes du mois
            if ($start->lt($monthStart)) {
                $start = $monthStart->copy();
            }
            if ($end->gt($monthEnd)) {
                $end = $monthEnd->copy();
            }
            for ($d = $start->copy(); $d <= $end; $d->addDay()) {
                $leaveDays[$d->format('Y-m-d')] = true;
            }
        }

        // Date d'embauche de l'employé (ne pas marquer absent avant cette date)
        $hireDate = $user->hire_date ? Carbon::parse($user->hire_date)->startOfDay() : null;

        for ($date = $monthStart->copy(); $date <= $monthEnd; $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $isWeekend = ! in_array($date->dayOfWeekIso, $workDays);
            $presence = $monthPresences->get($dateKey);
            $isOnLeave = isset($leaveDays[$dateKey]);
            $isFuture = $date->isFuture();
            $isBeforeHire = $hireDate && $date->lt($hireDate);

            $status = 'none';
            if ($isFuture) {
                $status = 'future';
            } elseif ($isBeforeHire) {
                // Jour avant l'embauche - ne pas marquer comme absent
                $status = 'none';
            } elseif ($isOnLeave) {
                $status = 'leave';
            } elseif ($presence) {
                // Vérifier si c'est une session de rattrapage
                if ($presence->is_recovery_session) {
                    $status = 'recovery';
                } elseif ($presence->is_late) {
                    $status = 'late';
                } else {
                    $status = 'present';
                }
            } elseif ($isWeekend) {
                $status = 'weekend';
            } else {
                $status = 'absent';
            }

            $calendarData[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->day,
                'status' => $status,
                'hours' => $presence ? round($presence->hours_worked ?? 0, 1) : null,
                'is_recovery' => $presence ? $presence->is_recovery_session : false,
            ];
        }

        // Vérifier si la géolocalisation est activée (au moins une zone active)
        $zones = GeolocationZone::where('is_active', true)->get();
        $geolocationEnabled = $zones->isNotEmpty();
        $defaultZone = GeolocationZone::getDefault();

        // Paramètres horaires
        $workSettings = [
            'work_start' => Setting::getWorkStartTime(),
            'work_end' => Setting::getWorkEndTime(),
            'break_start' => Setting::getBreakStartTime(),
            'break_end' => Setting::getBreakEndTime(),
            'late_tolerance' => Setting::getLateTolerance(),
        ];

        // Vérifier si aujourd'hui est un jour de travail
        $isWorkingDay = $user->isWorkingDay();
        $workDayNames = $user->work_day_names;

        // --- LOGIQUE STRICTE DE POINTAGE ---

        // 1. Restriction Horaire (17:00 Hard Limit)
        $lastCheckInTime = Carbon::today()->setHour(17)->setMinute(0);
        $isAfterHours = now()->gt($lastCheckInTime);

        // 2. Flags pour l'UI
        $canCheckIn = true;
        $checkInRestriction = null;

        if (! $isWorkingDay) {
            $canCheckIn = false;
            $checkInRestriction = 'not_working_day'; // Jour non travaillé
        } elseif (! $geolocationEnabled) {
            $canCheckIn = false;
            $checkInRestriction = 'no_geolocation'; // Pas de zone configurée
        } elseif ($isAfterHours) {
            $canCheckIn = false;
            $checkInRestriction = 'after_hours'; // Après 17h
        } elseif ($user->hasCheckedInToday()) {
            $canCheckIn = false;
            // Déjà pointé (géré ailleurs)
        }

        // 3. Flag pour le départ
        $canCheckOut = $todayPresence && ! $todayPresence->check_out;

        // 4. Session de rattrapage (jours non travaillés)
        // Permettre le pointage de rattrapage si:
        // - Ce n'est PAS un jour de travail normal
        // - L'employé a des heures à rattraper
        // - Il n'a pas déjà pointé aujourd'hui
        // - La géolocalisation est activée
        // - Il n'est pas après 17h
        $canStartRecoverySession = false;
        $recoverySessionInfo = null;

        if (! $isWorkingDay && $geolocationEnabled && ! $isAfterHours && ! $user->hasCheckedInToday()) {
            $hasLateToRecover = $totalUnrecoveredMinutes > 0;
            if ($hasLateToRecover) {
                $canStartRecoverySession = true;
                $recoverySessionInfo = [
                    'minutes_to_recover' => $totalUnrecoveredMinutes,
                    'formatted' => $this->formatMinutes($totalUnrecoveredMinutes),
                ];
            }
        }

        // Vérifier si la présence d'aujourd'hui est une session de rattrapage
        $isRecoverySessionToday = $todayPresence && $todayPresence->is_recovery_session;

        return view('employee.presences.index', compact(
            'presences',
            'todayPresence',
            'monthlyStats',
            'recoveryStats',
            'expiringLateData',
            'lateToRecover',
            'lateToRecoverCount',
            'totalUnrecoveredMinutes',
            'expiredLate',
            'geolocationEnabled',
            'defaultZone',
            'workSettings',
            'isWorkingDay',
            'workDayNames',
            'weeklyData',
            'weeklyLabels',
            'calendarData',
            'canCheckIn',
            'canCheckOut',
            'checkInRestriction',
            'isAfterHours',
            'canStartRecoverySession',
            'recoverySessionInfo',
            'isRecoverySessionToday'
        ));
    }

    /**
     * Format minutes as hours and minutes string
     */
    private function formatMinutes(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours}h".sprintf('%02d', $mins);
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$mins} min";
        }
    }

    /**
     * Démarrer une session de rattrapage (jour non travaillé)
     */
    public function startRecoverySession(Request $request)
    {
        $user = auth()->user();

        // Vérifier qu'il n'a pas déjà pointé aujourd'hui
        if ($user->hasCheckedInToday()) {
            return redirect()->back()->with('error', 'Vous avez déjà pointé aujourd\'hui.');
        }

        // Restriction horaire
        $limitTime = Carbon::today()->setHour(17)->setMinute(0);
        if (now()->gt($limitTime)) {
            return redirect()->back()->with('error', 'Session de rattrapage refusée. Il est passé 17h00.');
        }

        // Vérifier qu'il a des heures à rattraper
        $totalUnrecoveredMinutes = $user->presences()
            ->where('is_late', true)
            ->where('is_late_expired', false)
            ->selectRaw('SUM(GREATEST(0, CAST(late_minutes AS SIGNED) - CAST(recovery_minutes AS SIGNED))) as total')
            ->value('total') ?? 0;

        if ($totalUnrecoveredMinutes <= 0) {
            return redirect()->back()->with('error', 'Vous n\'avez pas d\'heures de retard à rattraper.');
        }

        // Vérifier la géolocalisation
        $zones = GeolocationZone::where('is_active', true)->get();
        if ($zones->isEmpty()) {
            return redirect()->back()->with('error', "Le pointage est désactivé car aucune zone de travail n'est configurée.");
        }

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if (! $latitude || ! $longitude) {
            return redirect()->back()->with('error', 'La géolocalisation est obligatoire. Veuillez autoriser l\'accès à votre position.');
        }

        // Vérifier la zone
        $checkInStatus = 'unknown';
        $geolocationZoneId = null;

        foreach ($zones as $zone) {
            if ($zone->isWithinZone((float) $latitude, (float) $longitude)) {
                $checkInStatus = 'in_zone';
                $geolocationZoneId = $zone->id;
                break;
            }
        }

        if ($checkInStatus !== 'in_zone') {
            $defaultZone = GeolocationZone::getDefault();
            $zoneName = $defaultZone ? $defaultZone->name : 'la zone autorisée';

            return redirect()->back()->with('error', "Pointage refusé : vous n'êtes pas dans $zoneName.");
        }

        // Créer la présence en mode "session de rattrapage"
        $workEndTime = Setting::getWorkEndTime();

        Presence::create([
            'user_id' => $user->id,
            'check_in' => now(),
            'date' => today(),
            'check_in_latitude' => $latitude,
            'check_in_longitude' => $longitude,
            'check_in_status' => $checkInStatus,
            'geolocation_zone_id' => $geolocationZoneId,
            'is_late' => false, // Ce n'est pas un retard, c'est une session de rattrapage
            'late_minutes' => null,
            'scheduled_start' => now()->format('H:i'), // L'heure d'arrivée = heure de début
            'scheduled_end' => $workEndTime,
            'is_recovery_session' => true, // Marquer comme session de rattrapage
        ]);

        $formatted = $this->formatMinutes($totalUnrecoveredMinutes);

        return redirect()->back()->with('success', "Session de rattrapage démarrée. Vous avez $formatted à rattraper. Tout le temps travaillé aujourd'hui sera comptabilisé comme rattrapage.");
    }

    /**
     * Terminer une session de rattrapage
     */
    public function endRecoverySession(Request $request)
    {
        $user = auth()->user();
        $presence = $user->todayPresence();

        if (! $presence) {
            return redirect()->back()->with('error', 'Aucune session de rattrapage en cours.');
        }

        if ($presence->check_out) {
            return redirect()->back()->with('error', 'Vous avez déjà terminé votre session de rattrapage.');
        }

        if (! $presence->is_recovery_session) {
            // Si ce n'est pas une session de rattrapage, utiliser le checkout normal
            return $this->checkOut($request);
        }

        // Vérifier la géolocalisation
        $zones = GeolocationZone::where('is_active', true)->get();
        $geolocationEnabled = $zones->isNotEmpty();

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $checkOutStatus = 'unknown';

        if ($geolocationEnabled) {
            if (! $latitude || ! $longitude) {
                return redirect()->back()->with('error', 'La géolocalisation est obligatoire.');
            }

            foreach ($zones as $zone) {
                if ($zone->isWithinZone((float) $latitude, (float) $longitude)) {
                    $checkOutStatus = 'in_zone';
                    break;
                }
            }

            if ($checkOutStatus !== 'in_zone') {
                $defaultZone = GeolocationZone::getDefault();
                $zoneName = $defaultZone ? $defaultZone->name : 'la zone autorisée';

                return redirect()->back()->with('error', "Pointage refusé : vous n'êtes pas dans $zoneName.");
            }
        }

        // Calculer le temps travaillé pendant la session de rattrapage
        $now = now();
        $checkIn = $presence->check_in;
        $workedMinutes = $checkIn->diffInMinutes($now);

        // Pour une session de rattrapage, TOUT le temps travaillé compte comme rattrapage
        // Limiter au solde d'heures à rattraper
        $lateBalance = $user->late_balance_minutes;
        $recoveryMinutes = min($workedMinutes, $lateBalance);

        // Si le temps travaillé dépasse le solde, le surplus est des heures supplémentaires normales
        $overtimeMinutes = max(0, $workedMinutes - $lateBalance);

        $presence->update([
            'check_out' => $now,
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
            'check_out_status' => $checkOutStatus,
            'departure_type' => 'recovery',
            'overtime_minutes' => $overtimeMinutes,
            'recovery_minutes' => $recoveryMinutes,
        ]);

        // Construire le message de succès
        $workedFormatted = $this->formatMinutes($workedMinutes);
        $recoveryFormatted = $this->formatMinutes($recoveryMinutes);
        $message = "Session de rattrapage terminée. Temps travaillé: $workedFormatted.";

        if ($recoveryMinutes > 0) {
            $message .= " Rattrapage appliqué: $recoveryFormatted.";

            // Vérifier le nouveau solde
            $newBalance = $lateBalance - $recoveryMinutes;
            if ($newBalance <= 0) {
                $message .= ' Félicitations ! Vous avez rattrapé toutes vos heures de retard.';
            } else {
                $newBalanceFormatted = $this->formatMinutes($newBalance);
                $message .= " Il vous reste $newBalanceFormatted à rattraper.";
            }
        }

        if ($overtimeMinutes > 0) {
            $overtimeFormatted = $this->formatMinutes($overtimeMinutes);
            $message .= " Heures supplémentaires (au-delà du rattrapage): $overtimeFormatted.";
        }

        return redirect()->back()->with('success', $message);
    }

    public function checkIn(Request $request)
    {
        $user = auth()->user();

        // Vérifier si c'est un jour de travail
        if (! $user->isWorkingDay()) {
            $workDays = $user->work_day_names ?: 'Aucun jour configuré';

            return redirect()->back()->with('error', "Aujourd'hui n'est pas un jour de travail pour vous. Vos jours de travail : $workDays");
        }

        if ($user->hasCheckedInToday()) {
            return redirect()->back()->with('error', 'Vous avez déjà pointé votre arrivée aujourd\'hui.');
        }

        // Restriction horaire stricte : 17h00
        $limitTime = Carbon::today()->setHour(17)->setMinute(0);
        if (now()->gt($limitTime)) {
            return redirect()->back()->with('error', "Pointage d'arrivée refusé. Il est passé 17h00.");
        }

        // Vérifier si la géolocalisation est activée
        $zones = GeolocationZone::where('is_active', true)->get();
        $geolocationEnabled = $zones->isNotEmpty();

        // Pré-requis Localisation (Bloquant)
        if (! $geolocationEnabled) {
            return redirect()->back()->with('error', "Le pointage est désactivé car aucune zone de travail n'est configurée. Contactez l'administrateur.");
        }

        // Géolocalisation
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $checkInStatus = 'unknown';
        $geolocationZoneId = null;

        if ($geolocationEnabled) {
            // Si la géolocalisation est activée, les coordonnées sont obligatoires
            if (! $latitude || ! $longitude) {
                return redirect()->back()->with('error', 'La géolocalisation est obligatoire. Veuillez autoriser l\'accès à votre position.');
            }

            // Vérifier si l'employé est dans une zone autorisée
            foreach ($zones as $zone) {
                if ($zone->isWithinZone((float) $latitude, (float) $longitude)) {
                    $checkInStatus = 'in_zone';
                    $geolocationZoneId = $zone->id;
                    break;
                }
            }

            // BLOQUER si hors zone
            if ($checkInStatus !== 'in_zone') {
                $defaultZone = GeolocationZone::getDefault();
                $zoneName = $defaultZone ? $defaultZone->name : 'la zone autorisée';

                return redirect()->back()->with('error', "Pointage refusé : vous n'êtes pas dans $zoneName. Veuillez vous rapprocher de votre lieu de travail.");
            }
        }

        // Récupérer les paramètres horaires
        $workStartTime = Setting::getWorkStartTime();
        $lateTolerance = Setting::getLateTolerance();
        $workEndTime = Setting::getWorkEndTime();

        // Calculer le retard
        $now = now();
        $scheduledStart = Carbon::createFromFormat('H:i', $workStartTime)->setDate($now->year, $now->month, $now->day);
        $lateThreshold = $scheduledStart->copy()->addMinutes($lateTolerance);

        $isLate = $now->gt($lateThreshold);
        $lateMinutes = $isLate ? (int) abs($now->diffInMinutes($scheduledStart)) : null;

        // Calculer la date limite de rattrapage si en retard
        $lateRecoveryDeadline = null;
        if ($isLate) {
            $recoveryDays = Setting::get('late_recovery_days', 7);
            $lateRecoveryDeadline = today()->addDays($recoveryDays);
        }

        Presence::create([
            'user_id' => $user->id,
            'check_in' => $now,
            'date' => today(),
            'check_in_latitude' => $latitude,
            'check_in_longitude' => $longitude,
            'check_in_status' => $checkInStatus,
            'geolocation_zone_id' => $geolocationZoneId,
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
            'late_recovery_deadline' => $lateRecoveryDeadline,
            'scheduled_start' => $workStartTime,
            'scheduled_end' => $workEndTime,
        ]);

        $message = 'Arrivée enregistrée avec succès.';
        if ($isLate) {
            $message .= " (Retard de $lateMinutes minutes - À rattraper avant le ".$lateRecoveryDeadline->format('d/m/Y').')';
        }

        return redirect()->back()->with('success', $message);
    }

    public function checkOut(Request $request)
    {
        $user = auth()->user();
        $presence = $user->todayPresence();

        if (! $presence) {
            return redirect()->back()->with('error', 'Vous n\'avez pas pointé votre arrivée aujourd\'hui.');
        }

        if ($presence->check_out) {
            return redirect()->back()->with('error', 'Vous avez déjà pointé votre départ aujourd\'hui.');
        }

        // Récupérer les paramètres horaires
        $workEndTime = Setting::getWorkEndTime();
        $now = now();
        $scheduledEnd = Carbon::createFromFormat('H:i', $workEndTime)->setDate($now->year, $now->month, $now->day);

        // Vérifier si c'est un départ anticipé (hors urgence)
        $isUrgency = $request->input('urgence') === '1';
        $urgencyReason = $request->input('urgency_reason');

        if (! $isUrgency && $now->lt($scheduledEnd)) {
            $remainingMinutes = $now->diffInMinutes($scheduledEnd);
            $remainingHours = floor($remainingMinutes / 60);
            $remainingMins = $remainingMinutes % 60;
            $timeRemaining = $remainingHours > 0 ? "{$remainingHours}h{$remainingMins}" : "{$remainingMins} min";

            return redirect()->back()->with('error', "Départ anticipé non autorisé. Il reste $timeRemaining avant l'heure de fin ($workEndTime). Utilisez l'option 'Départ d'urgence' si nécessaire.");
        }

        // Vérifier si la géolocalisation est activée
        $zones = GeolocationZone::where('is_active', true)->get();
        $geolocationEnabled = $zones->isNotEmpty();

        // Géolocalisation
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $checkOutStatus = 'unknown';

        if ($geolocationEnabled) {
            // Si la géolocalisation est activée, les coordonnées sont obligatoires
            if (! $latitude || ! $longitude) {
                return redirect()->back()->with('error', 'La géolocalisation est obligatoire. Veuillez autoriser l\'accès à votre position.');
            }

            // Vérifier si l'employé est dans une zone autorisée
            foreach ($zones as $zone) {
                if ($zone->isWithinZone((float) $latitude, (float) $longitude)) {
                    $checkOutStatus = 'in_zone';
                    break;
                }
            }

            // BLOQUER si hors zone
            if ($checkOutStatus !== 'in_zone') {
                $defaultZone = GeolocationZone::getDefault();
                $zoneName = $defaultZone ? $defaultZone->name : 'la zone autorisée';

                return redirect()->back()->with('error', "Pointage refusé : vous n'êtes pas dans $zoneName. Veuillez vous rapprocher de votre lieu de travail.");
            }
        }

        // Calculer le départ anticipé si urgence
        $isEarlyDeparture = $isUrgency && $now->lt($scheduledEnd);
        $earlyDepartureMinutes = $isEarlyDeparture ? $now->diffInMinutes($scheduledEnd) : null;

        // Calculer les heures supplémentaires (si départ après l'heure prévue)
        $overtimeMinutes = 0;
        if (! $isEarlyDeparture && $now->gt($scheduledEnd)) {
            $overtimeMinutes = $scheduledEnd->diffInMinutes($now);
        }

        // Vérifier si l'employé souhaite utiliser les heures sup comme rattrapage
        $isRecoverySession = $request->input('is_recovery_session') === '1';

        // Calculer le rattrapage automatique
        $recoveryMinutes = 0;
        if ($overtimeMinutes > 0) {
            // Récupérer le solde d'heures de retard AVANT cette présence
            $lateBalance = $user->late_balance_minutes;

            // Si l'employé a des heures à rattraper et a travaillé en heures sup
            if ($lateBalance > 0) {
                // Le rattrapage est automatique : on applique les heures sup au solde
                $recoveryMinutes = min($overtimeMinutes, $lateBalance);
            }
        }

        $presence->update([
            'check_out' => $now,
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
            'check_out_status' => $checkOutStatus,
            'is_early_departure' => $isEarlyDeparture,
            'early_departure_minutes' => $earlyDepartureMinutes,
            'departure_type' => $isUrgency ? 'urgence' : 'normal',
            'early_departure_reason' => $isUrgency ? $urgencyReason : null,
            'overtime_minutes' => $overtimeMinutes,
            'recovery_minutes' => $recoveryMinutes,
            'is_recovery_session' => $isRecoverySession || $recoveryMinutes > 0,
        ]);

        // Construire le message de succès
        $message = 'Départ enregistré avec succès.';
        if ($isEarlyDeparture) {
            $message .= " (Départ d'urgence - $earlyDepartureMinutes minutes avant l'heure prévue)";
        }
        if ($overtimeMinutes > 0) {
            $hours = floor($overtimeMinutes / 60);
            $mins = $overtimeMinutes % 60;
            $overtimeFormatted = $hours > 0 ? "{$hours}h".($mins > 0 ? sprintf('%02d', $mins) : '') : "{$mins} min";
            $message .= " Heures supplémentaires: $overtimeFormatted.";
        }
        if ($recoveryMinutes > 0) {
            $hours = floor($recoveryMinutes / 60);
            $mins = $recoveryMinutes % 60;
            $recoveryFormatted = $hours > 0 ? "{$hours}h".($mins > 0 ? sprintf('%02d', $mins) : '') : "{$mins} min";
            $message .= " Rattrapage appliqué: $recoveryFormatted.";
        }

        return redirect()->back()->with('success', $message);
    }
}
