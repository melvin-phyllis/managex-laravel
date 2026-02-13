<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeWorkDay;
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

        return view('employee.settings.index', [
            'currentWorkDays' => $currentWorkDays,
            'modificationsThisWeek' => $modificationsThisWeek,
            'maxModifications' => 2,
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
            'work_days' => 'required|array|min:3',
            'work_days.*' => 'integer|between:1,5',
        ], [
            'work_days.required' => 'Vous devez sélectionner vos jours de présence.',
            'work_days.min' => 'Vous devez sélectionner au minimum 3 jours.',
            'work_days.*.between' => 'Les jours doivent être du lundi au vendredi.',
        ]);

        $user = auth()->user();
        $weekStart = Carbon::now()->startOfWeek()->toDateString();

        // Vérifier la limite de modifications cette semaine
        $modificationsThisWeek = DB::table('work_day_modifications')
            ->where('user_id', $user->id)
            ->where('week_start', $weekStart)
            ->count();

        if ($modificationsThisWeek >= 2) {
            return redirect()->route('employee.settings.index')
                ->with('error', 'Limite atteinte : vous avez déjà modifié vos jours 2 fois cette semaine. Réinitialisation lundi.');
        }

        $newDays = array_map('intval', $request->work_days);
        sort($newDays);

        // Récupérer les anciens jours
        $oldDays = EmployeeWorkDay::where('user_id', $user->id)
            ->pluck('day_of_week')
            ->sort()
            ->values()
            ->toArray();

        // Vérifier si les jours sont identiques
        if ($oldDays === $newDays) {
            return redirect()->route('employee.settings.index')
                ->with('error', 'Les jours sélectionnés sont identiques aux jours actuels.');
        }

        DB::transaction(function () use ($user, $oldDays, $newDays, $weekStart) {
            // Supprimer les anciens jours
            EmployeeWorkDay::where('user_id', $user->id)->delete();

            // Créer les nouveaux jours
            foreach ($newDays as $day) {
                EmployeeWorkDay::create([
                    'user_id' => $user->id,
                    'day_of_week' => $day,
                ]);
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

        return redirect()->route('employee.settings.index')
            ->with('success', 'Jours de présence mis à jour avec succès.');
    }
}
