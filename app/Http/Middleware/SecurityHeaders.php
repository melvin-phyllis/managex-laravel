<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pour ajouter des en-têtes de sécurité HTTP
 * 
 * Ces en-têtes protègent contre:
 * - Clickjacking (X-Frame-Options)
 * - XSS (X-XSS-Protection, X-Content-Type-Options)
 * - MIME sniffing (X-Content-Type-Options)
 * - Information disclosure (X-Powered-By removal)
 * - Referrer leakage (Referrer-Policy)
 * - Permission policy (Permissions-Policy)
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Protection contre le clickjacking
        // SAMEORIGIN permet l'affichage dans des iframes du même domaine
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Protection contre le MIME sniffing
        // Empêche le navigateur de deviner le type MIME
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Protection XSS pour les anciens navigateurs
        // Les navigateurs modernes utilisent CSP à la place
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Politique de référent
        // Ne pas envoyer le referrer vers des sites externes
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Politique de permissions (remplace Feature-Policy)
        // Désactive les fonctionnalités sensibles par défaut
        $response->headers->set('Permissions-Policy', 'geolocation=(self), microphone=(), camera=()');

        // Supprimer l'en-tête X-Powered-By si présent (révèle la technologie serveur)
        $response->headers->remove('X-Powered-By');

        // En production, ajouter HSTS (HTTP Strict Transport Security)
        if (app()->environment('production')) {
            // Force HTTPS pendant 1 an, inclut les sous-domaines
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Content Security Policy (CSP) basique
        // À personnaliser selon les besoins de l'application
        // Note: Cette CSP est permissive pour permettre le développement
        // En production, il faudrait la renforcer
        if (app()->environment('production')) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
                "style-src 'self' 'unsafe-inline'; " .
                "img-src 'self' data: https:; " .
                "font-src 'self' data:; " .
                "connect-src 'self' wss:; " .
                "frame-ancestors 'self';"
            );
        }

        return $response;
    }
}
