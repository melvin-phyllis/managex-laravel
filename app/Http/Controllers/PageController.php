<?php

namespace App\Http\Controllers;

use App\Models\DemoRequest;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\DemoRequestNotification;
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
        $welcome = 'Bienvenue, '.auth()->user()->name.' !';

        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', $welcome);
        }

        return redirect()->route('employee.dashboard')->with('success', $welcome);
    }

    public function demoRequest()
    {
        return view('demo-request');
    }

    public function storeDemoRequest(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company_size' => ['required', 'string', 'in:1-10,11-50,51-200,200+'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $demo = DemoRequest::create($validated);

        // Notifier les admins par email
        $reportEmail = Setting::get('report_email');

        if ($reportEmail) {
            Notification::route('mail', $reportEmail)
                ->notify(new DemoRequestNotification($demo));
        } else {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new DemoRequestNotification($demo));
            }
        }

        return redirect()->route('demo-request')->with('success', 'Merci ! Notre équipe vous contactera sous 24h pour planifier votre démonstration.');
    }

    public function health()
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
