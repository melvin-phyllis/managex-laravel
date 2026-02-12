<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ManageX') }} - Completer votre profil</title>

    <meta name="theme-color" content="#4f46e5">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-2xl relative z-10">
            <!-- Logo & Header -->
            <div class="text-center mb-8">
                <div class="flex flex-row items-center justify-center mb-4 gap-4">
                    <x-application-logo class="w-14 h-14 rounded-full object-cover shadow-md" />
                    <span class="text-2xl font-bold text-gray-900 tracking-tight">{{ config('app.name', 'ManageX') }}</span>
                </div>
                <h1 class="text-xl font-bold text-gray-900">Bienvenue {{ $invitation->name }} !</h1>
                <p class="text-gray-500 mt-1">Completez vos informations pour activer votre compte</p>
            </div>

            <!-- Admin-provided info (read-only) -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <h3 class="text-sm font-semibold text-blue-800 mb-3">Informations de votre poste</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                    <div>
                        <span class="text-blue-600 font-medium">Poste :</span>
                        <span class="text-blue-900">{{ $invitation->poste }}</span>
                    </div>
                    <div>
                        <span class="text-blue-600 font-medium">Departement :</span>
                        <span class="text-blue-900">{{ $invitation->department->name }}</span>
                    </div>
                    <div>
                        <span class="text-blue-600 font-medium">Contrat :</span>
                        <span class="text-blue-900">{{ strtoupper($invitation->contract_type) }}</span>
                    </div>
                    <div>
                        <span class="text-blue-600 font-medium">Embauche :</span>
                        <span class="text-blue-900">{{ $invitation->hire_date->format('d/m/Y') }}</span>
                    </div>
                    @if($invitation->position)
                    <div>
                        <span class="text-blue-600 font-medium">Position :</span>
                        <span class="text-blue-900">{{ $invitation->position->name }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('invitation.complete', $invitation->token) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Section 1: Informations personnelles -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Informations personnelles
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Telephone -->
                            <div>
                                <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Telephone <span class="text-red-500">*</span></label>
                                <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}" required
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('telephone') border-red-500 @enderror"
                                       placeholder="+225 07 12 34 56 78">
                                @error('telephone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date de naissance -->
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date de naissance <span class="text-red-500">*</span></label>
                                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('date_of_birth') border-red-500 @enderror">
                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Genre -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Genre <span class="text-red-500">*</span></label>
                                <select name="gender" id="gender" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('gender') border-red-500 @enderror">
                                    <option value="">Selectionner</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Homme</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Femme</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Adresse -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Adresse
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="lg:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                                <input type="text" name="address" id="address" value="{{ old('address') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="123 rue de la Paix">
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Abidjan">
                            </div>
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                                <select name="country" id="country" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    @include('partials.country-options', ['selected' => old('country', 'CI')])
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Contact d'urgence -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Contact d'urgence
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du contact <span class="text-red-500">*</span></label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('emergency_contact_name') border-red-500 @enderror"
                                       placeholder="Marie Dupont">
                                @error('emergency_contact_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Telephone <span class="text-red-500">*</span></label>
                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" required
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('emergency_contact_phone') border-red-500 @enderror"
                                       placeholder="+225 07 12 34 56 78">
                                @error('emergency_contact_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-1">Lien de parente <span class="text-red-500">*</span></label>
                                <select name="emergency_contact_relationship" id="emergency_contact_relationship" required
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('emergency_contact_relationship') border-red-500 @enderror">
                                    <option value="">Selectionner</option>
                                    <option value="Conjoint(e)" {{ old('emergency_contact_relationship') == 'Conjoint(e)' ? 'selected' : '' }}>Conjoint(e)</option>
                                    <option value="Parent" {{ old('emergency_contact_relationship') == 'Parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="Enfant" {{ old('emergency_contact_relationship') == 'Enfant' ? 'selected' : '' }}>Enfant</option>
                                    <option value="Frere/Soeur" {{ old('emergency_contact_relationship') == 'Frere/Soeur' ? 'selected' : '' }}>Frere/Soeur</option>
                                    <option value="Ami(e)" {{ old('emergency_contact_relationship') == 'Ami(e)' ? 'selected' : '' }}>Ami(e)</option>
                                    <option value="Autre" {{ old('emergency_contact_relationship') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('emergency_contact_relationship')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Mot de passe -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Mot de passe
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe <span class="text-red-500">*</span></label>
                                <input type="password" name="password" id="password" required
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                                       placeholder="Minimum 8 caracteres">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Retapez votre mot de passe">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-center py-4">
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors flex items-center shadow-lg shadow-blue-500/25">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Finaliser mon inscription
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Toastify({
                    text: "{{ session('success') }}",
                    duration: 4000, close: true, gravity: "top", position: "right",
                    style: { background: "linear-gradient(to right, #10b981, #059669)", borderRadius: "10px" },
                }).showToast();
            @endif
            @if(session('error'))
                Toastify({
                    text: "{{ session('error') }}",
                    duration: 4000, close: true, gravity: "top", position: "right",
                    style: { background: "linear-gradient(to right, #ef4444, #b91c1c)", borderRadius: "10px" },
                }).showToast();
            @endif
        });
    </script>
</body>
</html>
