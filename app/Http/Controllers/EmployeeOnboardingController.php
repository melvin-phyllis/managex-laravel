<?php

namespace App\Http\Controllers;

use App\Models\EmployeeInvitation;
use App\Models\EmployeeWorkDay;
use App\Models\User;
use App\Traits\GeneratesEmployeeId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class EmployeeOnboardingController extends Controller
{
    use GeneratesEmployeeId;

    public function show(string $token)
    {
        $invitation = EmployeeInvitation::with(['department', 'position'])
            ->where('token', $token)
            ->first();

        if (! $invitation) {
            return view('invitation.expired', [
                'reason' => 'invalid',
                'message' => 'Ce lien d\'invitation est invalide.',
            ]);
        }

        if ($invitation->isCompleted()) {
            return view('invitation.expired', [
                'reason' => 'used',
                'message' => 'Cette invitation a déjà été utilisée.',
            ]);
        }

        if ($invitation->isExpired()) {
            return view('invitation.expired', [
                'reason' => 'expired',
                'message' => 'Ce lien d\'invitation a expiré.',
            ]);
        }

        return view('invitation.complete', compact('invitation'));
    }

    public function complete(Request $request, string $token)
    {
        $invitation = EmployeeInvitation::where('token', $token)->first();

        if (! $invitation || $invitation->isCompleted() || $invitation->isExpired()) {
            return redirect()->route('login')
                ->with('error', 'Ce lien d\'invitation n\'est plus valide.');
        }

        // Re-check email uniqueness at completion time
        if (User::where('email', $invitation->email)->exists()) {
            return redirect()->route('login')
                ->with('error', 'Un compte avec cet email existe déjà.');
        }

        $validated = $request->validate([
            'telephone' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'emergency_contact_relationship' => ['required', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'date_of_birth.required' => 'La date de naissance est obligatoire.',
            'date_of_birth.before' => 'La date de naissance doit être dans le passé.',
            'gender.required' => 'Le genre est obligatoire.',
            'emergency_contact_name.required' => 'Le nom du contact d\'urgence est obligatoire.',
            'emergency_contact_phone.required' => 'Le téléphone du contact d\'urgence est obligatoire.',
            'emergency_contact_relationship.required' => 'La relation avec le contact d\'urgence est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        try {
            DB::transaction(function () use ($invitation, $validated) {
                $employee = new User([
                    // From invitation (admin data)
                    'name' => $invitation->name,
                    'email' => $invitation->email,
                    'department_id' => $invitation->department_id,
                    'position_id' => $invitation->position_id,
                    'poste' => $invitation->poste,
                    'contract_type' => $invitation->contract_type,
                    'hire_date' => $invitation->hire_date,
                    'contract_end_date' => $invitation->contract_end_date,
                    'base_salary' => $invitation->base_salary,
                    'employee_id' => $this->generateEmployeeId(),
                    'leave_balance' => 25,
                    'rtt_balance' => 0,
                    'status' => 'active',
                    // From employee form
                    'telephone' => $validated['telephone'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'gender' => $validated['gender'],
                    'address' => $validated['address'],
                    'city' => $validated['city'],
                    'postal_code' => $validated['postal_code'],
                    'country' => $validated['country'] ?? 'CI',
                    'emergency_contact_name' => $validated['emergency_contact_name'],
                    'emergency_contact_phone' => $validated['emergency_contact_phone'],
                    'emergency_contact_relationship' => $validated['emergency_contact_relationship'],
                ]);

                $employee->password = Hash::make($validated['password']);
                $employee->role = 'employee';
                $employee->save();

                // Create work days
                foreach ($invitation->work_days as $day) {
                    EmployeeWorkDay::create([
                        'user_id' => $employee->id,
                        'day_of_week' => $day,
                    ]);
                }

                // Mark invitation as completed
                $invitation->update([
                    'completed_at' => now(),
                    'user_id' => $employee->id,
                ]);
            });

            return redirect()->route('login')
                ->with('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du compte via invitation: '.$e->getMessage());

            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.');
        }
    }
}
