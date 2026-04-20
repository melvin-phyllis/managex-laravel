<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegistrationCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $codes = RegistrationCode::with(['creator', 'user', 'department', 'position'])->latest()->paginate(15);
        $departments = \App\Models\Department::all();
        $positions = \App\Models\Position::all();

        return view('admin.registration-codes.index', compact('codes', 'departments', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,employee',
            'email' => 'nullable|email',
            'expires_at' => 'nullable|date|after:now',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
        ]);

        $code = RegistrationCode::create([
            'code' => strtoupper(Str::random(10)), // Code un peu plus long
            'role' => $request->role,
            'email' => $request->email,
            'expires_at' => $request->expires_at,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'created_by' => auth()->id(),
            'status' => 'active',
        ]);

        return back()->with('success', "Code généré avec succès : {$code->code}");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegistrationCode $registrationCode)
    {
        $registrationCode->delete();
        return back()->with('success', 'Code supprimé avec succès.');
    }
}
