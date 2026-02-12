<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmployeeInvitation;
use App\Notifications\EmployeeInvitationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class EmployeeInvitationController extends Controller
{
    public function create()
    {
        $departments = Department::getActiveCached();

        return view('admin.employee-invitations.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'department_id' => ['required', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'poste' => ['required', 'string', 'max:255'],
            'contract_type' => ['required', 'in:cdi,cdd,stage,alternance,freelance,interim'],
            'hire_date' => ['required', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after:hire_date'],
            'work_days' => ['required', 'array', 'min:1'],
            'work_days.*' => ['integer', 'between:1,7'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé par un employé existant.',
            'department_id.required' => 'Le département est obligatoire.',
            'poste.required' => 'Le poste est obligatoire.',
            'contract_type.required' => 'Le type de contrat est obligatoire.',
            'hire_date.required' => 'La date d\'embauche est obligatoire.',
            'work_days.required' => 'Sélectionnez au moins un jour de travail.',
            'work_days.min' => 'Sélectionnez au moins un jour de travail.',
        ]);

        // Check no pending invitation exists for this email
        $existingPending = EmployeeInvitation::where('email', $validated['email'])
            ->pending()
            ->exists();

        if ($existingPending) {
            return back()->withInput()->withErrors([
                'email' => 'Une invitation est déjà en attente pour cet email.',
            ]);
        }

        $invitation = EmployeeInvitation::create([
            'token' => bin2hex(random_bytes(32)),
            'email' => $validated['email'],
            'name' => $validated['name'],
            'department_id' => $validated['department_id'],
            'position_id' => $validated['position_id'] ?? null,
            'poste' => $validated['poste'],
            'contract_type' => $validated['contract_type'],
            'hire_date' => $validated['hire_date'],
            'contract_end_date' => $validated['contract_end_date'] ?? null,
            'work_days' => $validated['work_days'],
            'base_salary' => $validated['base_salary'] ?? null,
            'invited_by' => auth()->id(),
            'expires_at' => now()->addHours(48),
        ]);

        $emailSent = true;
        try {
            Notification::route('mail', $invitation->email)
                ->notify(new EmployeeInvitationNotification($invitation));
        } catch (\Exception $e) {
            $emailSent = false;
            \Log::warning("Impossible d'envoyer l'invitation à {$invitation->email}: ".$e->getMessage());
        }

        $invitationUrl = route('invitation.show', $invitation->token);

        if ($emailSent) {
            $message = 'Invitation envoyée avec succès à '.$invitation->email;
        } else {
            $message = 'Invitation créée mais l\'email n\'a pas pu être envoyé. Lien d\'invitation : '.$invitationUrl;
        }

        return redirect()->route('admin.employees.index')
            ->with($emailSent ? 'success' : 'warning', $message);
    }
}
