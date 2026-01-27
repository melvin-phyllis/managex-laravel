<?php

namespace App\Providers;

use App\Models\Payroll;
use App\Models\Task;
use App\Policies\PayrollPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Enregistrement des policies
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Payroll::class, PayrollPolicy::class);
        Gate::policy(\App\Models\Messaging\Conversation::class, \App\Policies\ConversationPolicy::class);
        Gate::policy(\App\Models\Messaging\Message::class, \App\Policies\MessagePolicy::class);
        Gate::policy(\App\Models\Document::class, \App\Policies\DocumentPolicy::class);

        // Enregistrement des observers pour les notifications
        Task::observe(\App\Observers\TaskObserver::class);
        \App\Models\Leave::observe(\App\Observers\LeaveObserver::class);
    }
}
