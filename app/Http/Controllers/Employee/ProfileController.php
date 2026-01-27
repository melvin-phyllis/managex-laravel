<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the employee's profile
     */
    public function index()
    {
        $user = auth()->user()->load(['department', 'position']);
        
        return view('employee.profile.index', compact('user'));
    }

    /**
     * Show edit form for personal information
     */
    public function edit()
    {
        $user = auth()->user()->load(['department', 'position']);
        
        return view('employee.profile.edit', compact('user'));
    }

    /**
     * Update personal information
     */
    public function updatePersonal(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            // Fiscal fields
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'children_count' => 'nullable|integer|min:0',
            'cnps_number' => 'nullable|string|max:30',
        ]);

        $user->update($validated);

        return redirect()->route('employee.profile.index')
            ->with('success', 'Informations personnelles mises à jour.');
    }

    /**
     * Update emergency contact
     */
    public function updateEmergencyContact(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
        ]);

        $user->update($validated);

        return redirect()->route('employee.profile.index')
            ->with('success', 'Contact d\'urgence mis à jour.');
    }

    /**
     * Update avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = auth()->user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return redirect()->route('employee.profile.index')
            ->with('success', 'Photo de profil mise à jour.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('employee.profile.index')
            ->with('success', 'Mot de passe mis à jour.');
    }

    /**
     * Update notification preferences (for messaging)
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
        ]);

        // Store in user preferences (could be JSON field or separate table)
        $user = auth()->user();
        $settings = $user->settings ?? [];
        $settings['email_notifications'] = $validated['email_notifications'] ?? false;
        $settings['push_notifications'] = $validated['push_notifications'] ?? false;
        
        // If settings is a JSON field on user model
        // $user->update(['settings' => $settings]);

        return redirect()->route('employee.profile.index')
            ->with('success', 'Préférences de notification mises à jour.');
    }
}
