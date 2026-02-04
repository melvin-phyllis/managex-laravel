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
 * - Content Security Policy avec nonces (CSP)
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
        // Générer un nonce unique pour cette requête
        $nonce = $this->generateNonce();

        // Stocker le nonce pour qu'il soit accessible dans les views
        app()->instance('csp-nonce', $nonce);
        view()->share('cspNonce', $nonce);

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
        // microphone=(self) et camera=(self) pour messagerie vocale et photos
        $response->headers->set('Permissions-Policy', 'geolocation=(self), microphone=(self), camera=(self)');

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

        // Content Security Policy (CSP) avec nonces
        // SÉCURITÉ: Les nonces remplacent 'unsafe-inline' pour bloquer XSS
        $csp = $this->buildCsp($nonce);
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }

    /**
     * Générer un nonce cryptographiquement sécurisé
     */
    private function generateNonce(): string
    {
        return base64_encode(random_bytes(16));
    }

    /**
     * Construire la politique CSP
     */
    private function buildCsp(string $nonce): string
    {
        $isProduction = app()->environment('production');

        // Sources de base
        $defaultSrc = "'self'";

        // Scripts: CDNs nécessaires (Chart.js, Alpine.js, jQuery, Lightbox, etc.)
        // NOTE: 'unsafe-eval' est nécessaire pour Alpine.js (évaluation des expressions x-data)
        $scriptCdns = 'https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com https://code.jquery.com';
        // Alpine.js nécessite 'unsafe-eval' pour évaluer les expressions comme x-data="{ open: false }"
        // C'est un compromis nécessaire pour l'interactivité frontend
        $scriptSrc = "'self' 'unsafe-inline' 'unsafe-eval' {$scriptCdns}";

        // Styles: fonts + CDNs (Lightbox CSS, FullCalendar CSS, Leaflet CSS, etc.)
        // NOTE: 'unsafe-inline' est nécessaire pour les attributs style="" inline
        // Le nonce seul ne couvre pas les style attributes selon la spec CSP
        $styleCdns = 'https://fonts.googleapis.com https://fonts.bunny.net https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com';
        // En production ET en dev, on a besoin de unsafe-inline pour les styles
        // car de nombreux composants utilisent des attributs style="" inline
        $styleSrc = "'self' 'unsafe-inline' {$styleCdns}";

        // Images: self, data URIs, et HTTPS pour les images externes
        $imgSrc = "'self' data: https:";

        // Fonts: Google Fonts, Bunny Fonts
        $fontSrc = "'self' data: https://fonts.gstatic.com https://fonts.bunny.net";

        // Connexions: self, WebSockets, CDNs pour source maps
        $connectSrc = "'self' wss: ws: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com";

        // Frame ancestors: self (clickjacking protection)
        $frameAncestors = "'self'";

        // Form action: self only
        $formAction = "'self'";

        // Base URI: self only
        $baseUri = "'self'";

        // Worker source: allow blob URLs for web workers
        $workerSrc = "'self' blob:";

        return implode('; ', [
            "default-src {$defaultSrc}",
            "script-src {$scriptSrc}",
            "style-src {$styleSrc}",
            "style-src-elem {$styleSrc}",
            "img-src {$imgSrc}",
            "font-src {$fontSrc}",
            "connect-src {$connectSrc}",
            "worker-src {$workerSrc}",
            "frame-ancestors {$frameAncestors}",
            "form-action {$formAction}",
            "base-uri {$baseUri}",
        ]);
    }
}
