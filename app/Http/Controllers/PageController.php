<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class PageController extends Controller
{
    public function home()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('landing');
    }

    public function dashboard()
    {
        $welcome = 'Bienvenue, ' . auth()->user()->name . ' !';

        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', $welcome);
        }

        return redirect()->route('employee.dashboard')->with('success', $welcome);
    }

    public function health()
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
