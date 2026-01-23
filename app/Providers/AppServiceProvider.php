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
    }
}
