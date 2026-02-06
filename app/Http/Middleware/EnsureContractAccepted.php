<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureContractAccepted
{
    protected array $whitelistedRoutes = [
        'employee.contract.accept',
        'employee.contract.refuse',
        'employee.contract.view-pdf',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user->role !== 'employee') {
            return $next($request);
        }

        $contract = $user->currentContract;

        if (! $contract || ! $contract->needsAcceptance()) {
            return $next($request);
        }

        if ($request->routeIs(...$this->whitelistedRoutes)) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Vous devez accepter votre contrat de travail.',
                'contract_acceptance_required' => true,
            ], 403);
        }

        View::share('needsContractAcceptance', true);
        View::share('pendingContract', $contract);

        return $next($request);
    }
}
