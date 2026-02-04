<?php

namespace App\Http\Controllers;

use App\Models\DemoRequest;
use Illuminate\Http\Request;

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
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('employee.dashboard');
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

        DemoRequest::create($validated);

        return redirect()->route('demo-request')->with('success', 'Merci ! Notre équipe vous contactera sous 24h pour planifier votre démonstration.');
    }
}
