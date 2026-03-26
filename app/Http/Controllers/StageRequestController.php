<?php

namespace App\Http\Controllers;

use App\Models\StageRequest;
use Illuminate\Http\Request;

class StageRequestController extends Controller
{
    public function create()
    {
        return view('stage-request');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'school' => ['nullable', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
            'desired_role' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        StageRequest::create($validated);

        return redirect()
            ->route('stage-request')
            ->with('success', 'Votre demande de stage a bien ete envoyee. Nous vous contacterons par email.');
    }
}

