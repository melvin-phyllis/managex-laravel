<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Determine the default dashboard based on role
        $user = auth()->user();
        $defaultRoute = match($user->role) {
            'admin' => route('admin.dashboard', absolute: false),
            default => route('employee.dashboard', absolute: false),
        };

        // Get the intended URL but reject AJAX/API endpoints
        $intended = session()->pull('url.intended');
        $ajaxPatterns = [
            'notifications/unread-count',
            'notifications/mark-all-read',
            'dashboard/activity',
            'presence-planning',
            'pre-check-in-status',
        ];

        $isAjaxUrl = false;
        if ($intended) {
            foreach ($ajaxPatterns as $pattern) {
                if (str_contains($intended, $pattern)) {
                    $isAjaxUrl = true;
                    break;
                }
            }
        }

        if ($intended && !$isAjaxUrl) {
            return redirect($intended);
        }

        return redirect($defaultRoute);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
