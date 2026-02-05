<x-layouts.admin>
    <div class="space-y-6" x-data="settingsPage()">
        <!-- Header avec gradient -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white">Paramétres</h1>
                            <p class="text-white/70 mt-1">Configuration générale du systéme</p>
                        </div>
                    </div>
                    
                    <!-- Admin info -->
                    <div class="flex items-center gap-3 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #8860D0, #5680E9);">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm">{{ auth()->user()->name }}</p>
                            <p class="text-white/60 text-xs">Administrateur</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1.5">
            <nav class="flex flex-wrap gap-1" aria-label="Tabs">
                <button @click="activeTab = 'compte'"
                        :class="activeTab === 'compte' ? 'text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100'"
                        :style="activeTab === 'compte' ? 'background: linear-gradient(135deg, #5680E9, #84CEEB)' : ''"
                        class="whitespace-nowrap py-2.5 px-4 font-medium text-sm flex items-center gap-2 transition-all rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Mon compte
                </button>
                <button @click="activeTab = 'horaires'"
                        :class="activeTab === 'horaires' ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-lg shadow-green-500/30' : 'text-gray-600 hover:bg-gray-100'"
                        class="whitespace-nowrap py-2.5 px-4 font-medium text-sm flex items-center gap-2 transition-all rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Horaires
                </button>
                <button @click="activeTab = 'pauses'"
                        :class="activeTab === 'pauses' ? 'bg-gradient-to-r from-orange-500 to-amber-600 text-white shadow-lg shadow-orange-500/30' : 'text-gray-600 hover:bg-gray-100'"
                        class="whitespace-nowrap py-2.5 px-4 font-medium text-sm flex items-center gap-2 transition-all rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                    </svg>
                    Pauses
                </button>
                <button @click="activeTab = 'retards'"
                        :class="activeTab === 'retards' ? 'bg-gradient-to-r from-red-500 to-rose-600 text-white shadow-lg shadow-red-500/30' : 'text-gray-600 hover:bg-gray-100'"
                        class="whitespace-nowrap py-2.5 px-4 font-medium text-sm flex items-center gap-2 transition-all rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Retards
                </button>
                <button @click="activeTab = 'organisation'"
                        :class="activeTab === 'organisation' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30' : 'text-gray-600 hover:bg-gray-100'"
                        class="whitespace-nowrap py-2.5 px-4 font-medium text-sm flex items-center gap-2 transition-all rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Organisation
                    <span class="bg-white/20 text-xs font-medium px-2 py-0.5 rounded-full" :class="activeTab === 'organisation' ? 'bg-white/20' : 'bg-blue-100 text-blue-700'">
                        {{ $departments->count() }}
                    </span>
                </button>
                <button @click="activeTab = 'paie'"
                        :class="activeTab === 'paie' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/30' : 'text-gray-600 hover:bg-gray-100'"
                        class="whitespace-nowrap py-2.5 px-4 font-medium text-sm flex items-center gap-2 transition-all rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Paie
                </button>
            </nav>
        </div>

        <!-- Tab: Compte -->
        <div x-show="activeTab === 'compte'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Changer l'email -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Changer mon email
                        </h3>
                    </div>
                    <form action="{{ route('admin.settings.update-email') }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label for="current_email" class="block text-sm font-medium text-gray-700 mb-1">Email actuel</label>
                                <input type="email" id="current_email" value="{{ auth()->user()->email }}" disabled
                                       class="w-full rounded-xl border-gray-300 bg-gray-50 text-gray-500">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Nouvel email</label>
                                <input type="email" name="email" id="email" required
                                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="nouveau@email.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email_password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                                <input type="password" name="password" id="email_password" required
                                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="w-full px-6 py-2.5 text-white font-medium rounded-xl transition-all shadow-lg" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                                Mettre é  jour l'email
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Changer le mot de passe -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4" style="background: linear-gradient(135deg, #8860D0, #C1C8E4);">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Changer mon mot de passe
                        </h3>
                    </div>
                    <form action="{{ route('admin.settings.update-password') }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                                <input type="password" name="current_password" id="current_password" required
                                       class="w-full rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500"
                                       placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                                <input type="password" name="new_password" id="new_password" required minlength="8"
                                       class="w-full rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500"
                                       placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                                @error('new_password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Minimum 8 caractéres</p>
                            </div>
                            
                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                                       class="w-full rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500"
                                       placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="w-full px-6 py-2.5 text-white font-medium rounded-xl transition-all shadow-lg" style="background: linear-gradient(135deg, #8860D0, #5680E9);">
                                Mettre é  jour le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Info sécurité -->
            <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-amber-800">Conseils de sécurité</h4>
                        <ul class="mt-1 text-sm text-amber-700 list-disc list-inside space-y-1">
                            <li>Utilisez un mot de passe fort avec des lettres, chiffres et caractéres spéciaux</li>
                            <li>Ne partagez jamais vos identifiants de connexion</li>
                            <li>Changez réguliérement votre mot de passe</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Horaires -->
        <div x-show="activeTab === 'horaires'" x-cloak>
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="section" value="horaires">

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center mb-6">
                        <div class="bg-green-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Horaires de travail</h2>
                            <p class="text-sm text-gray-500">Définissez les heures de début et de fin de journée pour tous les employés.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
                        <div>
                            <label for="work_start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Heure de début
                            </label>
                            <input type="time" name="work_start_time" id="work_start_time"
                                   value="{{ old('work_start_time', $settings['work_start_time']) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-lg py-3">
                            @error('work_start_time')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="work_end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Heure de fin
                            </label>
                            <input type="time" name="work_end_time" id="work_end_time"
                                   value="{{ old('work_end_time', $settings['work_end_time']) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-lg py-3">
                            @error('work_end_time')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-green-50 rounded-lg max-w-2xl">
                        <p class="text-sm text-green-800">
                            <strong>Durée de travail :</strong> Les employés travailleront de
                            <span class="font-mono">{{ $settings['work_start_time'] }}</span> é 
                            <span class="font-mono">{{ $settings['work_end_time'] }}</span>
                        </p>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                            Enregistrer les horaires
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tab: Pauses -->
        <div x-show="activeTab === 'pauses'" x-cloak>
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="section" value="pauses">

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center mb-6">
                        <div class="bg-orange-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Pause déjeuner</h2>
                            <p class="text-sm text-gray-500">La pause n'est pas comptabilisée dans les heures travaillées.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
                        <div>
                            <label for="break_start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Début de pause
                            </label>
                            <input type="time" name="break_start_time" id="break_start_time"
                                   value="{{ old('break_start_time', $settings['break_start_time']) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-lg py-3">
                            @error('break_start_time')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="break_end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Fin de pause
                            </label>
                            <input type="time" name="break_end_time" id="break_end_time"
                                   value="{{ old('break_end_time', $settings['break_end_time']) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-lg py-3">
                            @error('break_end_time')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-orange-50 rounded-lg max-w-2xl">
                        <p class="text-sm text-orange-800">
                            <strong>Durée de pause :</strong> De
                            <span class="font-mono">{{ $settings['break_start_time'] }}</span> é 
                            <span class="font-mono">{{ $settings['break_end_time'] }}</span>
                            (1 heure déduite automatiquement)
                        </p>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
                            Enregistrer les pauses
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tab: Retards -->
        <div x-show="activeTab === 'retards'" x-cloak>
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="section" value="retards">

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center mb-6">
                        <div class="bg-red-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Tolérance de retard</h2>
                            <p class="text-sm text-gray-500">Un employé est en retard si son arrivée dépasse l'heure de début + tolérance.</p>
                        </div>
                    </div>

                    <div class="max-w-md" x-data="{ tolerance: {{ $settings['late_tolerance_minutes'] }} }">
                        <label for="late_tolerance_minutes" class="block text-sm font-medium text-gray-700 mb-4">
                            Tolérance en minutes
                        </label>

                        <div class="flex items-center gap-4">
                            <input type="range" name="late_tolerance_minutes" id="late_tolerance_minutes"
                                   x-model="tolerance"
                                   min="0" max="60" step="5"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-red-600">
                            <span class="text-2xl font-bold text-gray-900 w-16 text-center" x-text="tolerance + ' min'"></span>
                        </div>

                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>0 min</span>
                            <span>30 min</span>
                            <span>60 min</span>
                        </div>

                        @error('late_tolerance_minutes')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6 p-4 bg-red-50 rounded-lg max-w-md">
                        <p class="text-sm text-red-800">
                            <strong>Exemple :</strong> Avec une tolérance de {{ $settings['late_tolerance_minutes'] }} min et un début é  {{ $settings['work_start_time'] }},
                            un employé est en retard é  partir de
                            <span class="font-mono font-bold">
                                @php
                                    $start = \Carbon\Carbon::createFromFormat('H:i', $settings['work_start_time']);
                                    echo $start->addMinutes($settings['late_tolerance_minutes'])->format('H:i');
                                @endphp
                            </span>
                        </p>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                            Enregistrer la tolérance
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tab: Organisation -->
        <div x-show="activeTab === 'organisation'" x-cloak>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Départements & Postes</h2>
                            <p class="text-sm text-gray-500">Gérez la structure organisationnelle de votre entreprise.</p>
                        </div>
                    </div>
                    <button @click="showDepartmentModal = true; editingDepartment = null; departmentForm = { name: '', description: '', color: '#3B82F6', is_active: true }"
                            class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nouveau département
                    </button>
                </div>

                <!-- Liste des départements -->
                <div class="space-y-4">
                    @forelse($departments as $department)
                        <div class="border border-gray-200 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <!-- Header du département -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 cursor-pointer hover:bg-gray-100 transition-colors" @click="open = !open">
                                <div class="flex items-center gap-3">
                                    <div class="w-4 h-4 rounded-full" style="background-color: {{ $department->color }}"></div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $department->name }}</h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $department->positions_count }} poste(s) Â· {{ $department->users_count }} employé(s)
                                        </p>
                                    </div>
                                    @if(!$department->is_active)
                                        <span class="px-2 py-0.5 text-xs font-medium bg-gray-200 text-gray-600 rounded-full">Inactif</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click.stop="openEditDepartment({{ $department->id }}, '{{ addslashes($department->name) }}', '{{ addslashes($department->description ?? '') }}', '{{ $department->color }}', {{ $department->is_active ? 'true' : 'false' }})"
                                            class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    @if($department->users_count == 0)
                                        <form action="{{ route('admin.settings.departments.destroy', $department) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce département ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" @click.stop class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Liste des postes -->
                            <div x-show="open" x-collapse>
                                <div class="p-4 border-t border-gray-200 bg-white">
                                    @if($department->description)
                                        <p class="text-sm text-gray-600 mb-4">{{ $department->description }}</p>
                                    @endif

                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-medium text-gray-700">Postes</h4>
                                        <button type="button" @click.stop="openCreatePosition({{ $department->id }}, '{{ addslashes($department->name) }}')"
                                                class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Ajouter un poste
                                        </button>
                                    </div>

                                    @if($department->positions->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($department->positions as $position)
                                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                    <div>
                                                        <span class="font-medium text-gray-900">{{ $position->name }}</span>
                                                        @if($position->description)
                                                            <p class="text-xs text-gray-500">{{ $position->description }}</p>
                                                        @endif
                                                        <span class="text-xs text-gray-400">{{ $position->users_count ?? 0 }} employé(s)</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        @if(!$position->is_active)
                                                            <span class="px-2 py-0.5 text-xs font-medium bg-gray-200 text-gray-600 rounded-full mr-2">Inactif</span>
                                                        @endif
                                                        <button type="button" @click.stop="openEditPosition({{ $position->id }}, {{ $department->id }}, '{{ addslashes($position->name) }}', '{{ addslashes($position->description ?? '') }}', {{ $position->is_active ? 'true' : 'false' }})"
                                                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </button>
                                                        @if(($position->users_count ?? 0) == 0)
                                                            <form action="{{ route('admin.settings.positions.destroy', $position) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce poste ?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" @click.stop class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 italic py-4 text-center">Aucun poste dans ce département</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun département</h3>
                            <p class="mt-1 text-sm text-gray-500">Commencez par créer un département pour organiser vos équipes.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Modal: Département -->
        <div x-show="showDepartmentModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDepartmentModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDepartmentModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showDepartmentModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <form :action="editingDepartment ? '{{ url('admin/settings/departments') }}/' + editingDepartment : '{{ route('admin.settings.departments.store') }}'" method="POST">
                        @csrf
                        <template x-if="editingDepartment">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div>
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" :class="editingDepartment ? 'bg-blue-100' : 'bg-green-100'">
                                <svg class="h-6 w-6" :class="editingDepartment ? 'text-blue-600' : 'text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="editingDepartment ? 'Modifier le département' : 'Nouveau département'"></h3>
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            <div>
                                <label for="dept_name" class="block text-sm font-medium text-gray-700">Nom *</label>
                                <input type="text" name="name" id="dept_name" x-model="departmentForm.name" required
                                       class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="dept_description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="dept_description" x-model="departmentForm.description" rows="2"
                                          class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                            <div>
                                <label for="dept_color" class="block text-sm font-medium text-gray-700">Couleur</label>
                                <div class="mt-1 flex items-center gap-3">
                                    <input type="color" name="color" id="dept_color" x-model="departmentForm.color"
                                           class="h-10 w-14 rounded border-gray-300 cursor-pointer">
                                    <span class="text-sm text-gray-500" x-text="departmentForm.color"></span>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="dept_is_active" x-model="departmentForm.is_active" value="1"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="dept_is_active" class="ml-2 text-sm text-gray-700">Département actif</label>
                            </div>
                        </div>

                        <div class="mt-6 sm:grid sm:grid-cols-2 sm:gap-3">
                            <button type="button" @click="showDepartmentModal = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                                Annuler
                            </button>
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:text-sm">
                                <span x-text="editingDepartment ? 'Modifier' : 'Créer'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: Poste -->
        <div x-show="showPositionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showPositionModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showPositionModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showPositionModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <form :action="editingPosition ? '{{ url('admin/settings/positions') }}/' + editingPosition : '{{ route('admin.settings.positions.store') }}'" method="POST">
                        @csrf
                        <template x-if="editingPosition">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        <input type="hidden" name="department_id" x-model="positionForm.department_id">

                        <div>
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" :class="editingPosition ? 'bg-blue-100' : 'bg-green-100'">
                                <svg class="h-6 w-6" :class="editingPosition ? 'text-blue-600' : 'text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="editingPosition ? 'Modifier le poste' : 'Nouveau poste'"></h3>
                                <p class="mt-1 text-sm text-gray-500">Département : <span class="font-medium" x-text="positionForm.department_name"></span></p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            <div>
                                <label for="pos_name" class="block text-sm font-medium text-gray-700">Nom du poste *</label>
                                <input type="text" name="name" id="pos_name" x-model="positionForm.name" required
                                       class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="pos_description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="pos_description" x-model="positionForm.description" rows="2"
                                          class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="pos_is_active" x-model="positionForm.is_active" value="1"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="pos_is_active" class="ml-2 text-sm text-gray-700">Poste actif</label>
                            </div>
                        </div>

                        <div class="mt-6 sm:grid sm:grid-cols-2 sm:gap-3">
                            <button type="button" @click="showPositionModal = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                                Annuler
                            </button>
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:text-sm">
                                <span x-text="editingPosition ? 'Modifier' : 'Créer'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tab: Paie -->
        <div x-show="activeTab === 'paie'" x-cloak>
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="section" value="paie">

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center mb-6">
                        <div class="bg-emerald-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Configuration Paie</h2>
                            <p class="text-sm text-gray-500">Sélectionnez le pays dont les régles fiscales seront appliquées aux fiches de paie.</p>
                        </div>
                    </div>

                    <div class="max-w-md">
                        <label for="payroll_country_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Pays de paie par défaut
                        </label>
                        <select name="payroll_country_id" id="payroll_country_id"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 py-3">
                            <option value="">-- Sélectionnez un pays --</option>
                            @foreach($payrollCountries as $country)
                                <option value="{{ $country->id }}" {{ ($settings['payroll_country_id'] ?? null) == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }} ({{ $country->currency_symbol }})
                                </option>
                            @endforeach
                        </select>
                        @error('payroll_country_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">
                            Les régles de calcul (IS, CN, IGR, CNPS...) du pays sélectionné seront utilisées pour toutes les fiches de paie.
                        </p>
                    </div>

                    @if(isset($payrollCountries) && $payrollCountries->count() > 0)
                        <div class="mt-6 p-4 bg-emerald-50 rounded-lg max-w-md">
                            <p class="text-sm text-emerald-800">
                                <strong>{{ $payrollCountries->count() }} pays configuré(s)</strong> dans le systéme.
                                <a href="{{ route('admin.payroll-settings.countries') }}" class="underline hover:no-underline">Gérer les pays â†’</a>
                            </p>
                        </div>
                    @else
                        <div class="mt-6 p-4 bg-yellow-50 rounded-lg max-w-md">
                            <p class="text-sm text-yellow-800">
                                <strong>Aucun pays configuré.</strong>
                                <a href="{{ route('admin.payroll-settings.countries') }}" class="underline hover:no-underline">Configurer un pays â†’</a>
                            </p>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                            Enregistrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script nonce="{{ $cspNonce ?? '' }}">
        function settingsPage() {
            return {
                activeTab: '{{ request('tab', 'compte') }}',
                showDepartmentModal: false,
                showPositionModal: false,
                editingDepartment: null,
                editingPosition: null,
                departmentForm: {
                    name: '',
                    description: '',
                    color: '#3B82F6',
                    is_active: true
                },
                positionForm: {
                    department_id: null,
                    department_name: '',
                    name: '',
                    description: '',
                    is_active: true
                },

                openEditDepartment(id, name, description, color, isActive) {
                    this.editingDepartment = id;
                    this.departmentForm = {
                        name: name,
                        description: description,
                        color: color,
                        is_active: isActive
                    };
                    this.showDepartmentModal = true;
                },

                openCreatePosition(departmentId, departmentName) {
                    this.editingPosition = null;
                    this.positionForm = {
                        department_id: departmentId,
                        department_name: departmentName,
                        name: '',
                        description: '',
                        is_active: true
                    };
                    this.showPositionModal = true;
                },

                openEditPosition(id, departmentId, name, description, isActive) {
                    this.editingPosition = id;
                    this.positionForm = {
                        department_id: departmentId,
                        department_name: '',
                        name: name,
                        description: description,
                        is_active: isActive
                    };
                    this.showPositionModal = true;
                }
            }
        }
    </script>
</x-layouts.admin>
