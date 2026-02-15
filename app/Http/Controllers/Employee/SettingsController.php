<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeWorkDay;
use App\Models\Presence;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $user = auth()->user();

        // Jours de travail actuels
        $currentWorkDays = EmployeeWorkDay::where('user_id', $user->id)
            ->pluck('day_of_week')
            ->toArray();

        // Compter les modifications cette semaine
        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $modificationsThisWeek = DB::table('work_day_modifications')
            ->where('user_id', $user->id)
            ->where('week_start', $weekStart)
            ->count();

        // Jours verrouillés (passés en semaine)
        $todayIso = Carbon::now()->dayOfWeekIso;
        $isWeekend = $todayIso >= 6;
        $lockedDays = [];
        if (!$isWeekend) {
            for ($d = 1; $d < $todayIso; $d++) {
                $lockedDays[] = $d;
            }
        }

        return view('employee.settings.index', [
            'currentWorkDays' => $currentWorkDays,
            'modificationsThisWeek' => $modificationsThisWeek,
            'maxModifications' => 2,
            'lockedDays' => $lockedDays,
            'isWeekend' => $isWeekend,
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($validated['password']);
        $user->saveQuietly();

        return redirect()->route('employee.settings.index')
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Update work days for the authenticated employee.
     */
    public function updateWorkDays(Request $request)
    {
        $request->validate([
            'work_days' => 'required|array|size:3',
            'work_days.*' => 'integer|between:1,5',
        ], [
            'work_days.required' => 'Vous devez sélectionner vos jours de présence.',
            'work_days.size' => 'Vous devez sélectionner exactement 3 jours.',
            'work_days.*.between' => 'Les jours doivent être du lundi au vendredi.',
        ]);

        $user = auth()->user();
        $weekStart = Carbon::now()->startOfWeek()->toDateString();

        $newDays = array_map('intval', $request->work_days);
        sort($newDays);

        // Récupérer les anciens jours
        $oldDays = EmployeeWorkDay::where('user_id', $user->id)
            ->pluck('day_of_week')
            ->sort()
            ->values()
            ->toArray();

        // Vérifier que les jours verrouillés n'ont pas été modifiés
        $todayIso = Carbon::now()->dayOfWeekIso;
        $isWeekend = $todayIso >= 6;
        if (!$isWeekend) {
            for ($d = 1; $d < $todayIso; $d++) {
                $wasSelected = in_array($d, $oldDays);
                $isNowSelected = in_array($d, $newDays);
                if ($wasSelected !== $isNowSelected) {
                    $dayName = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi'][$d];
                    return redirect()->route('employee.settings.index')
                        ->with('error', "$dayName est déjà passé et ne peut plus être modifié.");
                }
            }
        }

        // Vérifier la limite de modifications cette semaine
        $modificationsThisWeek = DB::table('work_day_modifications')
            ->where('user_id', $user->id)
            ->where('week_start', $weekStart)
            ->count();

        if ($modificationsThisWeek >= 2) {
            return redirect()->route('employee.settings.index')
                ->with('error', 'Limite atteinte : vous avez déjà modifié vos jours 2 fois cette semaine. Réinitialisation lundi.');
        }

        // Vérifier si les jours sont identiques
        if ($oldDays === $newDays) {
            return redirect()->route('employee.settings.index')
                ->with('error', 'Les jours sélectionnés sont identiques aux jours actuels.');
        }

        // Identifier les jours retirés (futurs) qui deviennent des absences
        $removedDays = array_diff($oldDays, $newDays);
        $absenceDays = [];
        $workStartTime = Setting::getWorkStartTime();
        $workEndTime = Setting::getWorkEndTime();

        foreach ($removedDays as $removedDay) {
            $dayDate = Carbon::now()->startOfWeek()->addDays($removedDay - 1);

            // Si weekend, les absences sont pour la semaine prochaine
            if ($isWeekend) {
                $dayDate = $dayDate->addWeek();
            }

            // Ne créer une absence que pour les jours futurs
            if ($dayDate->gte(today())) {
                $existingPresence = Presence::where('user_id', $user->id)
                    ->whereDate('date', $dayDate)
                    ->first();

                if (!$existingPresence) {
                    $absenceDays[] = $dayDate;
                }
            }
        }

        DB::transaction(function () use ($user, $oldDays, $newDays, $weekStart, $absenceDays, $workStartTime, $workEndTime) {
            // Supprimer les anciens jours
            EmployeeWorkDay::where('user_id', $user->id)->delete();

            // Créer les nouveaux jours
            foreach ($newDays as $day) {
                EmployeeWorkDay::create([
                    'user_id' => $user->id,
                    'day_of_week' => $day,
                ]);
            }

            // Créer les absences pour les jours retirés
            foreach ($absenceDays as $absenceDate) {
                Presence::create([
                    'user_id' => $user->id,
                    'date' => $absenceDate,
                    'check_in' => $absenceDate->copy()->setTimeFromTimeString($workStartTime),
                    'check_out' => $absenceDate->copy()->setTimeFromTimeString($workStartTime),
                    'is_absent' => true,
                    'absence_reason' => 'Jour retiré du planning',
                    'scheduled_start' => $absenceDate->copy()->setTimeFromTimeString($workStartTime),
                    'scheduled_end' => $absenceDate->copy()->setTimeFromTimeString($workEndTime),
                    'notes' => 'Absence automatique - jour de travail retiré du planning',
                ]);

                // Ajouter le temps de travail manqué au solde de retard
                $scheduledStart = Carbon::parse($workStartTime);
                $scheduledEnd = Carbon::parse($workEndTime);
                $workMinutes = $scheduledStart->diffInMinutes($scheduledEnd);

                $user->increment('late_balance_minutes', $workMinutes);
            }

            // Logger la modification
            DB::table('work_day_modifications')->insert([
                'user_id' => $user->id,
                'week_start' => $weekStart,
                'modified_at' => now(),
                'old_days' => json_encode($oldDays),
                'new_days' => json_encode($newDays),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $message = 'Jours de présence mis à jour avec succès.';
        if (count($absenceDays) > 0) {
            $count = count($absenceDays);
            $message .= " {$count} absence(s) enregistrée(s). Vous pouvez rattraper ces heures sur vos jours de repos.";
        }

        return redirect()->route('employee.settings.index')
            ->with('success', $message);
    }
}
