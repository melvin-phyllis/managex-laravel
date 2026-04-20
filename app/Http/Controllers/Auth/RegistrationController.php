<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistrationCode;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Etape 1 : Afficher le formulaire de vérification du code.
     */
    public function showVerify(): View
    {
        return view('auth.register-verify');
    }

    /**
     * Etape 1 : Vérifier le code et stocker en session.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'registration_code' => ['required', 'string'],
        ]);

        $regCode = RegistrationCode::where('code', $request->registration_code)
            ->where('status', 'active')
            ->first();

        if (!$regCode || !$regCode->isAvailable()) {
            return back()->withErrors(['registration_code' => 'Ce code d\'inscription est invalide ou expiré.']);
        }

        // Stocker le code validé en session
        session(['validated_registration_code' => $regCode->code]);

        return redirect()->route('register.complete');
    }

    /**
     * Etape 2 : Afficher le formulaire complet après validation du code.
     */
    public function showComplete(): View|RedirectResponse
    {
        $code = session('validated_registration_code');
        if (!$code) {
            return redirect()->route('register.verify');
        }

        $regCode = RegistrationCode::where('code', $code)->first();
        if (!$regCode || !$regCode->isAvailable()) {
            session()->forget('validated_registration_code');
            return redirect()->route('register.verify')->withErrors(['registration_code' => 'Session expirée.']);
        }

        $departments = Department::all();
        $positions = Position::all();

        return view('auth.register-complete', compact('regCode', 'departments', 'positions'));
    }

    /**
     * Etape 2 : Création finale du compte.
     */
    public function store(Request $request): RedirectResponse
    {
        $code = session('validated_registration_code');
        if (!$code) {
            return redirect()->route('register.verify');
        }

        $regCode = RegistrationCode::where('code', $code)->first();
        if (!$regCode || !$regCode->isAvailable()) {
            return redirect()->route('register.verify');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                'unique:' . User::class,
                function ($attribute, $value, $fail) use ($regCode) {
                    if ($regCode->email && strtolower($regCode->email) !== strtolower($value)) {
                        $fail('Cette adresse email n\'est pas autorisée à utiliser ce code d\'inscription.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'department_id' => ['required', 'exists:departments,id'],
            'position_id' => ['required', 'exists:positions,id'],
        ]);

        // Doublon de sécurité au cas où, bien que géré par la closure ci-dessus
        if ($regCode->email && strtolower($regCode->email) !== strtolower($request->email)) {
            return back()->withErrors(['email' => 'Cet email ne correspond pas au destinataire du code.'])->withInput();
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $regCode->role,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'hire_date' => now(),
            'status' => 'active',
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        // Marquer le code comme utilisé
        $regCode->update([
            'status' => 'used',
            'used_at' => now(),
            'used_by' => $user->id,
        ]);

        session()->forget('validated_registration_code');

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
