<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmployeeWorkDay;
use App\Models\Position;
use App\Models\User;
use App\Notifications\WelcomeEmployeeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'employee')->with(['department', 'position']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('poste', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filtre par département
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par type de contrat
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        // Charger les présences du jour pour chaque employé
        $employees = $query->orderBy('name')->paginate(10)->withQueryString();
        
        // Charger la présence du jour pour chaque employé
        $today = now()->toDateString();
        $employeeIds = $employees->pluck('id');
        $todayPresences = \App\Models\Presence::whereIn('user_id', $employeeIds)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('user_id');

        // Ajouter le statut de présence à chaque employé
        foreach ($employees as $employee) {
            $presence = $todayPresences->get($employee->id);
            if ($presence) {
                if ($presence->check_out) {
                    $employee->presence_status = 'completed'; // Journée terminée
                } else {
                    $employee->presence_status = 'present'; // Actuellement présent
                }
            } else {
                // Vérifier si en congé
                $isOnLeave = \App\Models\Leave::where('user_id', $employee->id)
                    ->where('statut', 'approved')
                    ->whereDate('date_debut', '<=', $today)
                    ->whereDate('date_fin', '>=', $today)
                    ->exists();
                $employee->presence_status = $isOnLeave ? 'on_leave' : 'absent';
            }
        }

        $departments = Department::active()->orderBy('name')->get();

        // Statistiques
        $totalEmployees = User::where('role', 'employee')->count();
        
        $presentToday = \App\Models\Presence::whereDate('date', $today)
            ->whereHas('user', fn($q) => $q->where('role', 'employee'))
            ->distinct('user_id')
            ->count('user_id');
        
        $onLeaveToday = \App\Models\Leave::where('statut', 'approved')
            ->whereDate('date_debut', '<=', $today)
            ->whereDate('date_fin', '>=', $today)
            ->whereHas('user', fn($q) => $q->where('role', 'employee'))
            ->distinct('user_id')
            ->count('user_id');
        
        $newThisMonth = User::where('role', 'employee')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $stats = [
            'total' => $totalEmployees,
            'present' => $presentToday,
            'on_leave' => $onLeaveToday,
            'new_this_month' => $newThisMonth,
        ];

        return view('admin.employees.index', compact('employees', 'departments', 'stats'));
    }

    public function create()
    {
        $departments = Department::active()->orderBy('name')->get();
        return view('admin.employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'poste' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'work_days' => ['required', 'array', 'min:1'],
            'work_days.*' => ['integer', 'between:1,7'],
            // Champs RH personnels
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            // Contact d'urgence
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            // Informations professionnelles
            'hire_date' => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after:hire_date'],
            'contract_type' => ['nullable', 'in:cdi,cdd,stage,alternance,freelance,interim'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users,employee_id'],
            // Informations administratives
            'social_security_number' => ['nullable', 'string', 'max:50'],
            'bank_iban' => ['nullable', 'string', 'max:50'],
            'bank_bic' => ['nullable', 'string', 'max:20'],
            // Soldes
            'leave_balance' => ['nullable', 'numeric', 'min:0'],
            'rtt_balance' => ['nullable', 'numeric', 'min:0'],
        ], [
            'work_days.required' => 'Veuillez sélectionner au moins un jour de travail.',
            'work_days.min' => 'Veuillez sélectionner au moins un jour de travail.',
            'contract_end_date.after' => 'La date de fin de contrat doit être postérieure à la date d\'embauche.',
        ]);

        // Générer un mot de passe aléatoire
        $password = Str::random(12);

        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => 'employee',
            'poste' => $request->poste,
            'telephone' => $request->telephone,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            // Champs RH
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'France',
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            'hire_date' => $request->hire_date ?? now(),
            'contract_end_date' => $request->contract_end_date,
            'contract_type' => $request->contract_type ?? 'cdi',
            'base_salary' => $request->base_salary,
            'employee_id' => $request->employee_id ?? $this->generateEmployeeId(),
            'social_security_number' => $request->social_security_number,
            'bank_iban' => $request->bank_iban,
            'bank_bic' => $request->bank_bic,
            'leave_balance' => $request->leave_balance ?? 25,
            'rtt_balance' => $request->rtt_balance ?? 0,
            'status' => 'active',
        ]);

        // Enregistrer les jours de travail
        foreach ($request->work_days as $day) {
            EmployeeWorkDay::create([
                'user_id' => $employee->id,
                'day_of_week' => $day,
            ]);
        }

        // Envoyer l'email de bienvenue avec les identifiants
        $employee->notify(new WelcomeEmployeeNotification($password, $employee->name));

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employé créé avec succès. Un email contenant les identifiants de connexion a été envoyé.');
    }

    /**
     * Générer un matricule unique pour l'employé
     */
    protected function generateEmployeeId(): string
    {
        $prefix = 'EMP';
        $year = date('Y');
        $lastEmployee = User::where('employee_id', 'like', "{$prefix}{$year}%")
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastEmployee && preg_match('/(\d+)$/', $lastEmployee->employee_id, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function show(User $employee)
    {
        $employee->load(['department', 'position',
                        'presences' => fn($q) => $q->latest()->take(10),
                        'tasks' => fn($q) => $q->latest()->take(5),
                        'leaves' => fn($q) => $q->latest()->take(5),
                        'payrolls' => fn($q) => $q->latest()->take(6)]);

        return view('admin.employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        $employee->load('workDays');
        $departments = Department::active()->orderBy('name')->get();
        $positions = $employee->department_id
            ? Position::where('department_id', $employee->department_id)->active()->orderBy('name')->get()
            : collect();
        return view('admin.employees.edit', compact('employee', 'departments', 'positions'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employee->id],
            'poste' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'work_days' => ['required', 'array', 'min:1'],
            'work_days.*' => ['integer', 'between:1,7'],
            // Champs RH personnels
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            // Contact d'urgence
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            // Informations professionnelles
            'hire_date' => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after:hire_date'],
            'contract_type' => ['nullable', 'in:cdi,cdd,stage,alternance,freelance,interim'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users,employee_id,' . $employee->id],
            // Informations administratives
            'social_security_number' => ['nullable', 'string', 'max:50'],
            'bank_iban' => ['nullable', 'string', 'max:50'],
            'bank_bic' => ['nullable', 'string', 'max:20'],
            // Soldes
            'leave_balance' => ['nullable', 'numeric', 'min:0'],
            'rtt_balance' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:active,on_leave,suspended,terminated'],
        ], [
            'work_days.required' => 'Veuillez sélectionner au moins un jour de travail.',
            'work_days.min' => 'Veuillez sélectionner au moins un jour de travail.',
            'contract_end_date.after' => 'La date de fin de contrat doit être postérieure à la date d\'embauche.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'poste' => $request->poste,
            'telephone' => $request->telephone,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            // Champs RH
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            'hire_date' => $request->hire_date,
            'contract_end_date' => $request->contract_end_date,
            'contract_type' => $request->contract_type,
            'base_salary' => $request->base_salary,
            'employee_id' => $request->employee_id,
            'social_security_number' => $request->social_security_number,
            'bank_iban' => $request->bank_iban,
            'bank_bic' => $request->bank_bic,
            'leave_balance' => $request->leave_balance,
            'rtt_balance' => $request->rtt_balance,
            'status' => $request->status,
            'notes' => $request->notes,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        // Mettre à jour les jours de travail
        $employee->workDays()->delete();
        foreach ($request->work_days as $day) {
            EmployeeWorkDay::create([
                'user_id' => $employee->id,
                'day_of_week' => $day,
            ]);
        }

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employé mis à jour avec succès.');
    }

    public function destroy(User $employee)
    {
        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employé supprimé avec succès.');
    }
}
