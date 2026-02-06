<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias pour le middleware de rôle
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'contract.accepted' => \App\Http\Middleware\EnsureContractAccepted::class,
        ]);

        // Ajouter les en-têtes de sécurité à toutes les requêtes web
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\CheckAccountStatus::class,
        ]);

        // SÉCURITÉ: Configuration des proxies de confiance
        // En production, remplacer '*' par les IPs de vos proxies/load balancers
        // Exemple: TRUSTED_PROXIES=192.168.1.1,10.0.0.0/8
        $trustedProxies = env('TRUSTED_PROXIES');
        if ($trustedProxies) {
            $middleware->trustProxies(at: explode(',', $trustedProxies));
        } elseif (env('APP_ENV') === 'local') {
            $middleware->trustProxies(at: '*');
        }
        // Note: En production sans TRUSTED_PROXIES défini, aucun proxy n'est approuvé
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
