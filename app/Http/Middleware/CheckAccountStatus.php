<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Vérifie que le compte de l'utilisateur connecté n'est pas bloqué.
     * Déconnecte automatiquement les utilisateurs suspendus ou terminés.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Vérifier si le compte est suspendu ou terminé
            if (in_array($user->status, ['suspended', 'terminated'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                $message = $user->status === 'suspended'
                    ? 'Votre compte a été suspendu. Veuillez contacter l\'administration.'
                    : 'Votre compte a été désactivé. Veuillez contacter l\'administration.';
                
                return redirect()->route('login')->withErrors([
                    'email' => $message,
                ]);
            }
        }

        return $next($request);
    }
}
