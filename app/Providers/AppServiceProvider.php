<?php

namespace App\Providers;

use App\Models\Payroll;
use App\Models\Task;
use App\Policies\PayrollPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use NotificationChannels\WebPush\WebPushChannel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix pour MySQL < 5.7.7 ou MariaDB < 10.2.2 (limite de clé à 1000 bytes)
        Schema::defaultStringLength(191);

        // Forcer HTTPS en production (nécessaire derrière un proxy comme Railway/Render)
        if (config('app.env') === 'production' || app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Enregistrement des policies
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Payroll::class, PayrollPolicy::class);
        Gate::policy(\App\Models\Messaging\Conversation::class, \App\Policies\ConversationPolicy::class);
        Gate::policy(\App\Models\Messaging\Message::class, \App\Policies\MessagePolicy::class);
        Gate::policy(\App\Models\Document::class, \App\Policies\DocumentPolicy::class);

        // Enregistrement des observers pour les notifications
        Task::observe(\App\Observers\TaskObserver::class);
        \App\Models\Leave::observe(\App\Observers\LeaveObserver::class);

        // Enregistrement du canal de notification WebPush
        $this->app->make(ChannelManager::class)->extend('webpush', function ($app) {
            return $app->make(WebPushChannel::class);
        });

        // Configuration du Rate Limiting
        $this->configureRateLimiting();
    }

    /**
     * Configure les limites de requêtes pour protéger l'API
     */
    protected function configureRateLimiting(): void
    {
        // Limite globale API: 60 requêtes par minute par utilisateur
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Limite pour la messagerie: 30 messages par minute
        RateLimiter::for('messaging', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        // Limite pour les uploads: 10 par minute
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        // Limite pour les exports/rapports: 15 par minute (opérations lourdes)
        RateLimiter::for('exports', function (Request $request) {
            return Limit::perMinute(15)->by($request->user()?->id ?: $request->ip());
        });

        // Limite stricte pour le login: 5 tentatives par minute
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email').'|'.$request->ip());
        });

        // Limite pour les actions sensibles (suppression, etc.): 20 par minute
        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });

        // Limite pour les requêtes IA: 10 par minute par utilisateur
        RateLimiter::for('ai', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
