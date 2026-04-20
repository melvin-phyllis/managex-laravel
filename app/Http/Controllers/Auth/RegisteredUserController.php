<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'registration_code' => ['required', 'string'],
        ]);

        $regCode = \App\Models\RegistrationCode::where('code', $request->registration_code)
            ->where('status', 'active')
            ->first();

        if (!$regCode || !$regCode->isAvailable()) {
            return back()->withErrors(['registration_code' => 'Ce code d\'inscription est invalide ou expiré.'])
                ->withInput();
        }

        if ($regCode->email && $regCode->email !== $request->email) {
            return back()->withErrors(['registration_code' => 'Ce code est réservé à une autre adresse email.'])
                ->withInput();
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $regCode->role, // Attribue le rôle défini dans le code
        ]);
        $user->password = Hash::make($request->password);
        $user->save();

        // Marquer le code comme utilisé
        $regCode->update([
            'status' => 'used',
            'used_at' => now(),
            'used_by' => $user->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
