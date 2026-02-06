<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     * Vérifie d'abord si le token est valide et non expiré.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $email = $request->query('email');
        $token = $request->route('token');

        // Vérifier si le token existe et n'est pas expiré
        if ($email && $token) {
            $record = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->first();

            if (!$record) {
                return redirect()->route('login')
                    ->with('error', 'Ce lien a expiré. Veuillez demander un nouveau lien de réinitialisation.');
            }

            // Vérifier l'expiration (config auth.passwords.users.expire en minutes)
            $expireMinutes = config('auth.passwords.users.expire', 5);
            $createdAt = \Carbon\Carbon::parse($record->created_at);

            if ($createdAt->addMinutes($expireMinutes)->isPast()) {
                // Supprimer le token expiré
                DB::table('password_reset_tokens')->where('email', $email)->delete();

                return redirect()->route('login')
                    ->with('error', 'Ce lien a expiré. Veuillez demander un nouveau lien de réinitialisation.');
            }

            // Vérifier que le token correspond (hashé en BDD)
            if (!Hash::check($token, $record->token)) {
                return redirect()->route('login')
                    ->with('error', 'Ce lien est invalide. Veuillez demander un nouveau lien de réinitialisation.');
            }
        }

        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Gérer le cas d'un token expiré avec un message explicite
        if ($status === Password::INVALID_TOKEN) {
            return redirect()->route('login')
                ->with('error', 'Ce lien a expiré. Veuillez demander un nouveau lien de réinitialisation.');
        }

        // If the password was successfully reset, we will redirect the user back to
        // the application's login view.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
