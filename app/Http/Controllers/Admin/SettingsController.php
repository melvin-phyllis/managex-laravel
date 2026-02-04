<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\PayrollCountry;
use App\Models\Position;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Afficher la page des paramètres
     */
    public function index()
    {
        $settings = [
            'work_start_time' => Setting::getWorkStartTime(),
            'work_end_time' => Setting::getWorkEndTime(),
            'break_start_time' => Setting::getBreakStartTime(),
            'break_end_time' => Setting::getBreakEndTime(),
            'late_tolerance_minutes' => Setting::getLateTolerance(),
            'payroll_country_id' => Setting::get('payroll_country_id'),
        ];

        $payrollCountries = PayrollCountry::active()->orderBy('name')->get();

        $departments = Department::withCount(['positions', 'users'])
            ->with(['positions' => function ($query) {
                $query->withCount('users');
            }])
            ->orderBy('name')
            ->get();

        return view('admin.settings.index', compact('settings', 'departments', 'payrollCountries'));
    }

    /**
     * Mettre à jour les paramètres
     */
    public function update(Request $request)
    {
        $section = $request->input('section', 'all');

        switch ($section) {
            case 'horaires':
                $validated = $request->validate([
                    'work_start_time' => 'required|date_format:H:i',
                    'work_end_time' => 'required|date_format:H:i|after:work_start_time',
                ], [
                    'work_start_time.required' => 'L\'heure de début est obligatoire.',
                    'work_end_time.required' => 'L\'heure de fin est obligatoire.',
                    'work_end_time.after' => 'L\'heure de fin doit être après l\'heure de début.',
                ]);
                Setting::set('work_start_time', $validated['work_start_time'], 'time', 'presence');
                Setting::set('work_end_time', $validated['work_end_time'], 'time', 'presence');
                $message = 'Horaires mis à jour avec succès.';
                $tab = 'horaires';
                break;

            case 'pauses':
                $validated = $request->validate([
                    'break_start_time' => 'required|date_format:H:i',
                    'break_end_time' => 'required|date_format:H:i|after:break_start_time',
                ], [
                    'break_start_time.required' => 'Le début de pause est obligatoire.',
                    'break_end_time.required' => 'La fin de pause est obligatoire.',
                    'break_end_time.after' => 'La fin de pause doit être après le début de pause.',
                ]);
                Setting::set('break_start_time', $validated['break_start_time'], 'time', 'presence');
                Setting::set('break_end_time', $validated['break_end_time'], 'time', 'presence');
                $message = 'Pauses mises à jour avec succès.';
                $tab = 'pauses';
                break;

            case 'retards':
                $validated = $request->validate([
                    'late_tolerance_minutes' => 'required|integer|min:0|max:120',
                ], [
                    'late_tolerance_minutes.min' => 'La tolérance doit être positive.',
                    'late_tolerance_minutes.max' => 'La tolérance ne peut pas dépasser 120 minutes.',
                ]);
                Setting::set('late_tolerance_minutes', $validated['late_tolerance_minutes'], 'integer', 'presence');
                $message = 'Tolérance de retard mise à jour avec succès.';
                $tab = 'retards';
                break;

            case 'paie':
                $validated = $request->validate([
                    'payroll_country_id' => 'required|exists:payroll_countries,id',
                ], [
                    'payroll_country_id.required' => 'Le pays de paie est obligatoire.',
                    'payroll_country_id.exists' => 'Le pays sélectionné n\'existe pas.',
                ]);
                Setting::set('payroll_country_id', $validated['payroll_country_id'], 'integer', 'payroll');
                $message = 'Pays de paie mis à jour avec succès.';
                $tab = 'paie';
                break;

            default:
                $validated = $request->validate([
                    'work_start_time' => 'required|date_format:H:i',
                    'work_end_time' => 'required|date_format:H:i|after:work_start_time',
                    'break_start_time' => 'required|date_format:H:i',
                    'break_end_time' => 'required|date_format:H:i|after:break_start_time',
                    'late_tolerance_minutes' => 'required|integer|min:0|max:120',
                ]);
                Setting::set('work_start_time', $validated['work_start_time'], 'time', 'presence');
                Setting::set('work_end_time', $validated['work_end_time'], 'time', 'presence');
                Setting::set('break_start_time', $validated['break_start_time'], 'time', 'presence');
                Setting::set('break_end_time', $validated['break_end_time'], 'time', 'presence');
                Setting::set('late_tolerance_minutes', $validated['late_tolerance_minutes'], 'integer', 'presence');
                $message = 'Paramètres mis à jour avec succès.';
                $tab = 'horaires';
        }

        return redirect()->route('admin.settings.index', ['tab' => $tab])
            ->with('success', $message);
    }

    /**
     * Créer un département
     */
    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|max:7',
            'is_active' => 'sometimes|boolean',
        ], [
            'name.required' => 'Le nom du département est obligatoire.',
            'name.unique' => 'Ce département existe déjà.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Department::create($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'organisation'])
            ->with('success', 'Département créé avec succès.');
    }

    /**
     * Modifier un département
     */
    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,'.$department->id,
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|max:7',
            'is_active' => 'sometimes|boolean',
        ], [
            'name.required' => 'Le nom du département est obligatoire.',
            'name.unique' => 'Ce département existe déjà.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $department->update($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'organisation'])
            ->with('success', 'Département modifié avec succès.');
    }

    /**
     * Supprimer un département
     */
    public function destroyDepartment(Department $department)
    {
        if ($department->users()->exists()) {
            return redirect()->route('admin.settings.index', ['tab' => 'organisation'])
                ->with('error', 'Impossible de supprimer un département avec des employés.');
        }

        // Supprimer d'abord les postes associés
        $department->positions()->delete();
        $department->delete();

        return redirect()->route('admin.settings.index', ['tab' => 'organisation'])
            ->with('success', 'Département supprimé avec succès.');
    }

    /**
     * Créer un poste
     */
    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ], [
            'name.required' => 'Le nom du poste est obligatoire.',
            'department_id.required' => 'Le département est obligatoire.',
            'department_id.exists' => 'Le département sélectionné n\'existe pas.',
        ]);

        // Vérifier l'unicité du poste dans le département
        $exists = Position::where('department_id', $validated['department_id'])
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return redirect()->route('admin.settings.index', ['tab' => 'organisation'])
                ->with('error', 'Ce poste existe déjà dans ce département.');
        }

        $validated['is_active'] = $request->has('is_active');

        Position::create($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'organisation'])
            ->with('success', 'Poste créé avec succès.');
    }

    /**
     * Modifier un poste
     */
    public function updatePosition(Request $request, Position $position)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ], [
            'name.required' => 'Le nom du poste est obligatoire.',
        ]);

        // Vérifier l'unicité du poste dans le département
        $exists = Position::where('department_id', $validated['department_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $position->id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.settings.index', ['tab' => 'organisation'])
                ->with('error', 'Ce poste existe déjà dans ce département.');
        }

        $validated['is_active'] = $request->has('is_active');

        $position->update($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'organisation'])
            ->with('success', 'Poste modifié avec succès.');
    }

    /**
     * Supprimer un poste
     */
    public function destroyPosition(Position $position)
    {
        if ($position->users()->exists()) {
            return redirect()->route('admin.settings.index', ['tab' => 'organisation'])
                ->with('error', 'Impossible de supprimer un poste avec des employés.');
        }

        $position->delete();

        return redirect()->route('admin.settings.index', ['tab' => 'organisation'])
            ->with('success', 'Poste supprimé avec succès.');
    }

    /**
     * Mettre à jour l'email de l'administrateur
     */
    public function updateEmail(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'required|string',
        ], [
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire pour confirmer cette action.',
        ]);

        // Vérifier le mot de passe actuel
        if (! Hash::check($validated['password'], $user->password)) {
            return redirect()->route('admin.settings.index', ['tab' => 'compte'])
                ->with('error', 'Le mot de passe est incorrect.');
        }

        $user->update(['email' => $validated['email']]);

        return redirect()->route('admin.settings.index', ['tab' => 'compte'])
            ->with('success', 'Email mis à jour avec succès.');
    }

    /**
     * Mettre à jour le mot de passe de l'administrateur
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'new_password.required' => 'Le nouveau mot de passe est obligatoire.',
            'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        // Vérifier le mot de passe actuel
        if (! Hash::check($validated['current_password'], $user->password)) {
            return redirect()->route('admin.settings.index', ['tab' => 'compte'])
                ->with('error', 'Le mot de passe actuel est incorrect.');
        }

        $user->password = Hash::make($validated['new_password']);
        $user->saveQuietly();

        return redirect()->route('admin.settings.index', ['tab' => 'compte'])
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }
}
