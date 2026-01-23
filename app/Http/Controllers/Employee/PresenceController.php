<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\GeolocationZone;
use App\Models\Presence;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        ];

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
            ->keyBy(fn($p) => $p->date->format('Y-m-d'));
        
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
            if ($start->lt($monthStart)) $start = $monthStart->copy();
            if ($end->gt($monthEnd)) $end = $monthEnd->copy();
            for ($d = $start->copy(); $d <= $end; $d->addDay()) {
                $leaveDays[$d->format('Y-m-d')] = true;
            }
        }
        
        for ($date = $monthStart->copy(); $date <= $monthEnd; $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $isWeekend = !in_array($date->dayOfWeekIso, $workDays);
            $presence = $monthPresences->get($dateKey);
            $isOnLeave = isset($leaveDays[$dateKey]);
            $isFuture = $date->isFuture();
            
            $status = 'none';
            if ($isFuture) {
                $status = 'future';
            } elseif ($isWeekend) {
                $status = 'weekend';
            } elseif ($isOnLeave) {
                $status = 'leave';
            } elseif ($presence) {
                if ($presence->is_late) {
                    $status = 'late';
                } else {
                    $status = 'present';
                }
            } else {
                $status = 'absent';
            }
            
            $calendarData[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->day,
                'status' => $status,
                'hours' => $presence ? round($presence->hours_worked ?? 0, 1) : null,
            ];
        }

        // Vérifier si la géolocalisation est activée (au moins une zone active)
        $geolocationEnabled = GeolocationZone::where('is_active', true)->exists();
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

        return view('employee.presences.index', compact(
            'presences',
            'todayPresence',
            'monthlyStats',
            'geolocationEnabled',
            'defaultZone',
            'workSettings',
            'isWorkingDay',
            'workDayNames',
            'weeklyData',
            'weeklyLabels',
            'calendarData'
        ));
    }

    public function checkIn(Request $request)
    {
        $user = auth()->user();

        // Vérifier si c'est un jour de travail
        if (!$user->isWorkingDay()) {
            $workDays = $user->work_day_names ?: 'Aucun jour configuré';
            return redirect()->back()->with('error', "Aujourd'hui n'est pas un jour de travail pour vous. Vos jours de travail : $workDays");
        }

        if ($user->hasCheckedInToday()) {
            return redirect()->back()->with('error', 'Vous avez déjà pointé votre arrivée aujourd\'hui.');
        }

        // Vérifier si la géolocalisation est activée
        $zones = GeolocationZone::where('is_active', true)->get();
        $geolocationEnabled = $zones->isNotEmpty();

        // Géolocalisation
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $checkInStatus = 'unknown';
        $geolocationZoneId = null;

        if ($geolocationEnabled) {
            // Si la géolocalisation est activée, les coordonnées sont obligatoires
            if (!$latitude || !$longitude) {
                return redirect()->back()->with('error', 'La géolocalisation est obligatoire. Veuillez autoriser l\'accès à votre position.');
            }

            // Vérifier si l'employé est dans une zone autorisée
            foreach ($zones as $zone) {
                if ($zone->isWithinZone((float)$latitude, (float)$longitude)) {
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
        $lateMinutes = $isLate ? $now->diffInMinutes($scheduledStart) : null;

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
            'scheduled_start' => $workStartTime,
            'scheduled_end' => $workEndTime,
        ]);

        $message = 'Arrivée enregistrée avec succès.';
        if ($isLate) {
            $message .= " (Retard de $lateMinutes minutes)";
        }

        return redirect()->back()->with('success', $message);
    }

    public function checkOut(Request $request)
    {
        $user = auth()->user();
        $presence = $user->todayPresence();

        if (!$presence) {
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

        if (!$isUrgency && $now->lt($scheduledEnd)) {
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
            if (!$latitude || !$longitude) {
                return redirect()->back()->with('error', 'La géolocalisation est obligatoire. Veuillez autoriser l\'accès à votre position.');
            }

            // Vérifier si l'employé est dans une zone autorisée
            foreach ($zones as $zone) {
                if ($zone->isWithinZone((float)$latitude, (float)$longitude)) {
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

        $presence->update([
            'check_out' => $now,
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
            'check_out_status' => $checkOutStatus,
            'is_early_departure' => $isEarlyDeparture,
            'early_departure_minutes' => $earlyDepartureMinutes,
            'departure_type' => $isUrgency ? 'urgence' : 'normal',
            'early_departure_reason' => $isUrgency ? $urgencyReason : null,
        ]);

        $message = 'Départ enregistré avec succès.';
        if ($isEarlyDeparture) {
            $message .= " (Départ d'urgence - $earlyDepartureMinutes minutes avant l'heure prévue)";
        }

        return redirect()->back()->with('success', $message);
    }
}
