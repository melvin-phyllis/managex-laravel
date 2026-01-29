<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header avec horloge en temps réel -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mes présences</h1>
                <p class="text-sm text-gray-500 mt-1">Suivez votre temps de travail et votre ponctualité</p>
            </div>
            <!-- Horloge en temps réel -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4 rounded-2xl shadow-lg" x-data="{ time: '' }" x-init="
                setInterval(() => {
                    time = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                }, 1000);
            ">
                <div class="text-xs uppercase tracking-wide opacity-80">Heure actuelle</div>
                <div class="text-2xl font-bold font-mono" x-text="time"></div>
            </div>
        </div>

        <!-- Horaires de travail -->
        @if(isset($workSettings))
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-sm border border-gray-200 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="bg-blue-100 p-2.5 rounded-xl">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Horaires</p>
                            <p class="font-semibold text-gray-900">{{ $workSettings['work_start'] }} - {{ $workSettings['work_end'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="bg-orange-100 p-2.5 rounded-xl">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Pause</p>
                            <p class="font-semibold text-gray-900">{{ $workSettings['break_start'] }} - {{ $workSettings['break_end'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="bg-yellow-100 p-2.5 rounded-xl">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tolérance retard</p>
                            <p class="font-semibold text-gray-900">{{ $workSettings['late_tolerance'] }} min</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Vos jours de travail:</span>
                    <span class="font-medium text-indigo-700">{{ $workDayNames ?? 'Non définis' }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Avertissement jour non travaillé -->
        @if(isset($isWorkingDay) && !$isWorkingDay)
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-amber-700">
                        <strong>Aujourd'hui n'est pas un jour de travail pour vous.</strong> Le pointage est désactivé.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Avertissement Pas de Zone Configurée -->
        @if(isset($checkInRestriction) && $checkInRestriction === 'no_geolocation')
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        <strong>Configuration manquante :</strong> Aucune zone de travail n'est assignée. Le pointage est impossible.
                        <br> Veuillez contacter votre administrateur.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Avertissement Après 17h -->
        @if(isset($checkInRestriction) && $checkInRestriction === 'after_hours')
        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-lg mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-orange-700">
                        <strong>Pointage fermé :</strong> Il est passé 17h00. Les arrivées ne sont plus acceptées.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Pointage du jour - Design amélioré -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Pointage du jour</h2>
                <span class="text-sm text-gray-500">{{ now()->translatedFormat('l d F Y') }}</span>
            </div>

            <!-- Info géolocalisation -->
            @if($geolocationEnabled)
                <div class="mb-4 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Géolocalisation obligatoire</strong> - Votre position sera enregistrée lors du pointage.
                                @if($defaultZone)
                                    Zone autorisée: {{ $defaultZone->name }} (rayon {{ $defaultZone->radius }}m)
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Message d'erreur géolocalisation -->
                <div id="geoError" class="mb-4 hidden bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800" id="geoErrorTitle">Géolocalisation requise</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p id="geoErrorMessage">Vous devez activer la géolocalisation pour pointer.</p>
                                <button type="button" onclick="location.reload()" class="mt-3 inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Rafraîchir la page
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-4">
                @if(!$todayPresence)
                    <!-- Formulaire d'arrivée amélioré -->
                    <form id="checkInForm" action="{{ route('employee.presences.check-in') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="latitude" id="checkInLat">
                        <input type="hidden" name="longitude" id="checkInLng">
                        <button type="button" id="checkInBtn" 
                                {{ isset($canCheckIn) && !$canCheckIn ? 'disabled' : '' }}
                                class="w-full px-6 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-medium rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg shadow-green-500/30 flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none transform hover:scale-[1.02] active:scale-[0.98] disabled:hover:scale-100">
                            <svg id="checkInIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span id="checkInText" class="text-lg">
                                {{ (isset($canCheckIn) && !$canCheckIn) ? 'Pointage non disponible' : "Pointer l'arrivée" }}
                            </span>
                        </button>
                    </form>
                @elseif(!$todayPresence->check_out)
                    <!-- Arrivée pointée + Timer -->
                    <div class="flex-1 px-6 py-4 {{ $todayPresence->is_late ? 'bg-gradient-to-r from-orange-50 to-amber-50 border-orange-200' : 'bg-gradient-to-r from-green-50 to-emerald-50 border-green-200' }} border rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold {{ $todayPresence->is_late ? 'text-orange-800' : 'text-green-800' }}">
                                    ✓ Arrivée pointée à {{ $todayPresence->check_in->format('H:i') }}
                                </p>
                                @if($todayPresence->is_late)
                                    <p class="text-sm text-orange-600 mt-1">⚠️ Retard de {{ abs($todayPresence->late_minutes) }} minutes</p>
                                @endif
                                @if($todayPresence->check_in_status === 'in_zone')
                                    <p class="text-sm text-green-600 mt-1">📍 Dans la zone autorisée</p>
                                @endif
                            </div>
                            <!-- Timer de travail en cours -->
                            <div class="text-right" x-data="{ elapsed: '' }" x-init="
                                const start = new Date('{{ $todayPresence->check_in->toIso8601String() }}');
                                setInterval(() => {
                                    const now = new Date();
                                    const diff = Math.floor((now - start) / 1000);
                                    const h = Math.floor(diff / 3600);
                                    const m = Math.floor((diff % 3600) / 60);
                                    elapsed = h + 'h ' + m.toString().padStart(2, '0') + 'm';
                                }, 1000);
                            ">
                                <p class="text-xs text-gray-500">Temps de travail</p>
                                <p class="text-xl font-bold text-blue-600 font-mono" x-text="elapsed"></p>
                            </div>
                        </div>
                        
                        <!-- Barre de progression vers 8h -->
                        <div class="mt-4" x-data="{ progress: 0 }" x-init="
                            const start = new Date('{{ $todayPresence->check_in->toIso8601String() }}');
                            const target = 8 * 60; // 8 heures en minutes
                            setInterval(() => {
                                const now = new Date();
                                const elapsed = (now - start) / 1000 / 60; // en minutes
                                progress = Math.min(100, Math.round((elapsed / target) * 100));
                            }, 1000);
                        ">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span>Progression journalière</span>
                                <span x-text="progress + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-1000" :style="'width: ' + progress + '%'"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaires de départ -->
                    <div class="flex-1 space-y-2" x-data="{ showUrgencyModal: false, urgencyReason: '' }">
                        <!-- Formulaire de départ normal -->
                        <form id="checkOutForm" action="{{ route('employee.presences.check-out') }}" method="POST">
                            @csrf
                            <input type="hidden" name="latitude" id="checkOutLat">
                            <input type="hidden" name="longitude" id="checkOutLng">
                            <button type="button" id="checkOutBtn" class="w-full px-6 py-4 bg-gradient-to-r from-red-500 to-rose-600 text-white font-medium rounded-xl hover:from-red-600 hover:to-rose-700 transition-all shadow-lg shadow-red-500/30 flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] active:scale-[0.98]">
                                <svg id="checkOutIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span id="checkOutText" class="text-lg">Pointer le départ</span>
                            </button>
                        </form>

                        <!-- Bouton de départ d'urgence -->
                        <button type="button" @click="showUrgencyModal = true" class="w-full px-4 py-2 bg-amber-100 text-amber-700 text-sm font-medium rounded-xl hover:bg-amber-200 transition-colors flex items-center justify-center gap-2 border border-amber-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Départ d'urgence
                        </button>

                        <!-- Modal d'urgence -->
                        <div x-show="showUrgencyModal" 
                             x-cloak 
                             x-on:keydown.escape.window="showUrgencyModal = false"
                             class="fixed inset-0 z-50 flex items-center justify-center" 
                             aria-modal="true"
                             style="margin: 0;">
                            <!-- Backdrop -->
                            <div x-show="showUrgencyModal" 
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0" 
                                 x-transition:enter-end="opacity-100" 
                                 x-transition:leave="ease-in duration-200" 
                                 x-transition:leave-start="opacity-100" 
                                 x-transition:leave-end="opacity-0" 
                                 class="fixed inset-0 bg-gray-500/75 transition-opacity" 
                                 @click="showUrgencyModal = false"></div>
                            <!-- Modal Content -->
                            <div x-show="showUrgencyModal" 
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0 scale-95" 
                                 x-transition:enter-end="opacity-100 scale-100" 
                                 x-transition:leave="ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="relative bg-white rounded-2xl shadow-xl transform transition-all w-full max-w-lg mx-4 p-6">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100">
                                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center">
                                    <h3 class="text-lg font-semibold text-gray-900">Départ d'urgence</h3>
                                    <p class="text-sm text-gray-500 mt-2">Veuillez indiquer la raison de votre départ anticipé.</p>
                                    <textarea x-model="urgencyReason" rows="3" class="mt-4 w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Raison du départ d'urgence..."></textarea>
                                </div>
                                <div class="mt-5 grid grid-cols-2 gap-3">
                                    <button type="button" @click="showUrgencyModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">Annuler</button>
                                    <form id="urgencyCheckOutForm" action="{{ route('employee.presences.check-out') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="urgence" value="1">
                                        <input type="hidden" name="early_departure_reason" x-bind:value="urgencyReason">
                                        <input type="hidden" name="latitude" id="urgencyCheckOutLat">
                                        <input type="hidden" name="longitude" id="urgencyCheckOutLng">
                                        <button type="button" id="urgencyCheckOutBtn" class="w-full px-4 py-2 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-colors">Confirmer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Journée terminée -->
                    <div class="flex-1 px-6 py-4 bg-gradient-to-r from-gray-50 to-slate-100 border border-gray-200 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-800">✅ Journée terminée</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $todayPresence->check_in->format('H:i') }} → {{ $todayPresence->check_out->format('H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Durée totale</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $todayPresence->hours_worked }}h</p>
                            </div>
                        </div>
                        @if($todayPresence->is_late || $todayPresence->is_early_departure)
                        <div class="mt-3 flex gap-2">
                            @if($todayPresence->is_late)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700">⚠️ Retard: {{ $todayPresence->late_minutes }} min</span>
                            @endif
                            @if($todayPresence->is_early_departure)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">🚨 Départ anticipé: {{ $todayPresence->early_departure_minutes }} min</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 flex items-center justify-center">
                        <div class="text-center py-8">
                            <div class="text-5xl mb-2">🎉</div>
                            <p class="text-gray-600 font-medium">Bonne fin de journée !</p>
                            <p class="text-sm text-gray-400">À demain</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Stats Cards améliorées -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Jours pointés -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-2.5 rounded-xl shadow-lg shadow-green-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3">{{ $monthlyStats['days_present'] }}</p>
                <p class="text-xs text-gray-500">Jours pointés</p>
            </div>

            <!-- Heures totales -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-2.5 rounded-xl shadow-lg shadow-blue-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3">{{ number_format($monthlyStats['total_hours'], 1) }}h</p>
                <p class="text-xs text-gray-500">Heures totales</p>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ min(100, ($monthlyStats['total_hours'] / max(1, $monthlyStats['target_hours'])) * 100) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Objectif: {{ $monthlyStats['target_hours'] }}h</p>
                </div>
            </div>

            <!-- Score de ponctualité -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="bg-gradient-to-br from-purple-500 to-violet-600 p-2.5 rounded-xl shadow-lg shadow-purple-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold {{ $monthlyStats['punctuality_score'] >= 80 ? 'text-green-600' : ($monthlyStats['punctuality_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }} mt-3">{{ $monthlyStats['punctuality_score'] }}%</p>
                <p class="text-xs text-gray-500">Ponctualité</p>
            </div>

            <!-- Heures supplémentaires -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-2.5 rounded-xl shadow-lg shadow-amber-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-amber-600 mt-3">{{ $monthlyStats['overtime_hours'] }}h</p>
                <p class="text-xs text-gray-500">Heures sup.</p>
            </div>

            <!-- Retards -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="bg-gradient-to-br from-red-500 to-rose-600 p-2.5 rounded-xl shadow-lg shadow-red-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-red-600 mt-3">{{ $monthlyStats['total_late'] }}</p>
                <p class="text-xs text-gray-500">Retards ({{ $monthlyStats['total_late_minutes'] }} min)</p>
            </div>
        </div>

        <!-- Graphique + Calendrier -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Graphique hebdomadaire -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Heures des 7 derniers jours</h3>
                <div class="relative" style="height: 200px;">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>

            <!-- Calendrier mensuel -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 Calendrier du mois</h3>
                <div class="grid grid-cols-7 gap-1 text-center text-xs mb-2">
                    <span class="text-gray-500 font-medium">Lun</span>
                    <span class="text-gray-500 font-medium">Mar</span>
                    <span class="text-gray-500 font-medium">Mer</span>
                    <span class="text-gray-500 font-medium">Jeu</span>
                    <span class="text-gray-500 font-medium">Ven</span>
                    <span class="text-gray-500 font-medium">Sam</span>
                    <span class="text-gray-500 font-medium">Dim</span>
                </div>
                <div class="grid grid-cols-7 gap-1">
                    @php
                        $firstDayOfMonth = \Carbon\Carbon::now()->startOfMonth();
                        $startPadding = $firstDayOfMonth->dayOfWeekIso - 1;
                    @endphp
                    
                    {{-- Padding pour le premier jour --}}
                    @for($i = 0; $i < $startPadding; $i++)
                        <div class="aspect-square"></div>
                    @endfor
                    
                    @foreach($calendarData as $day)
                        @php
                            $statusClasses = [
                                'present' => 'bg-green-100 text-green-800 border-green-200',
                                'late' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'absent' => 'bg-red-100 text-red-800 border-red-200',
                                'leave' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'weekend' => 'bg-gray-50 text-gray-400',
                                'future' => 'bg-gray-50 text-gray-400',
                            ];
                            $class = $statusClasses[$day['status']] ?? 'bg-gray-50 text-gray-400';
                            $isToday = $day['date'] === now()->format('Y-m-d');
                        @endphp
                        <div class="aspect-square flex items-center justify-center text-xs font-medium rounded-lg border {{ $class }} {{ $isToday ? 'ring-2 ring-blue-500 ring-offset-1' : '' }}" title="{{ $day['hours'] ? $day['hours'].'h travaillées' : '' }}">
                            {{ $day['day'] }}
                        </div>
                    @endforeach
                </div>
                
                <!-- Légende -->
                <div class="mt-4 flex flex-wrap gap-3 text-xs">
                    <div class="flex items-center gap-1"><span class="w-3 h-3 bg-green-100 border border-green-200 rounded"></span> Présent</div>
                    <div class="flex items-center gap-1"><span class="w-3 h-3 bg-yellow-100 border border-yellow-200 rounded"></span> Retard</div>
                    <div class="flex items-center gap-1"><span class="w-3 h-3 bg-red-100 border border-red-200 rounded"></span> Absent</div>
                    <div class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-100 border border-blue-200 rounded"></span> Congé</div>
                    <div class="flex items-center gap-1"><span class="w-3 h-3 bg-gray-50 border border-gray-200 rounded"></span> Non travaillé</div>
                </div>
            </div>
        </div>

        <!-- Month Selector -->
        <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-200">
            <form action="{{ route('employee.presences.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="mois" class="block text-sm font-medium text-gray-700 mb-1">Afficher l'historique du mois</label>
                    <input type="month" name="mois" id="mois" value="{{ request('mois', now()->format('Y-m')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25">
                    Afficher
                </button>
            </form>
        </div>

        <!-- Historique Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">📋 Historique des présences</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Arrivée</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Départ</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Durée</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Localisation</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($presences as $presence)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $presence->date->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $presence->date->translatedFormat('l') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $presence->is_late ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $presence->check_in->format('H:i') }}
                                        @if($presence->is_late)
                                            <span class="ml-1">(+{{ $presence->late_minutes }}min)</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($presence->check_out)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $presence->is_early_departure ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $presence->check_out->format('H:i') }}
                                            @if($presence->is_early_departure)
                                                <span class="ml-1">(-{{ $presence->early_departure_minutes }}min)</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 animate-pulse">
                                            En cours...
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $presence->hours_worked ? number_format($presence->hours_worked, 1) . 'h' : '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($presence->is_late || $presence->is_early_departure)
                                        <div class="flex flex-wrap gap-1">
                                            @if($presence->is_late)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700">Retard</span>
                                            @endif
                                            @if($presence->is_early_departure)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">{{ $presence->departure_type === 'urgence' ? '🚨 Urgence' : 'Anticipé' }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">✓ OK</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($presence->check_in_status === 'in_zone')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Zone</span>
                                    @elseif($presence->check_in_status === 'out_of_zone')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">⚠ Hors zone</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-gray-100 rounded-full p-4 mb-4">
                                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Aucune présence pour cette période</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($presences->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50">
                    {{ $presences->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Graphique hebdomadaire
        new Chart(document.getElementById('weeklyChart'), {
            type: 'bar',
            data: {
                labels: @json($weeklyLabels),
                datasets: [{
                    label: 'Heures travaillées',
                    data: @json($weeklyData),
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        ticks: { stepSize: 2 }
                    }
                }
            }
        });
    </script>

    @if($geolocationEnabled)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const geoError = document.getElementById('geoError');
            const geoErrorTitle = document.getElementById('geoErrorTitle');
            const geoErrorMessage = document.getElementById('geoErrorMessage');

            const checkInBtn = document.getElementById('checkInBtn');
            const checkInForm = document.getElementById('checkInForm');
            const checkInLat = document.getElementById('checkInLat');
            const checkInLng = document.getElementById('checkInLng');
            const checkInText = document.getElementById('checkInText');
            const checkInIcon = document.getElementById('checkInIcon');

            const checkOutBtn = document.getElementById('checkOutBtn');
            const checkOutForm = document.getElementById('checkOutForm');
            const checkOutLat = document.getElementById('checkOutLat');
            const checkOutLng = document.getElementById('checkOutLng');
            const checkOutText = document.getElementById('checkOutText');
            const checkOutIcon = document.getElementById('checkOutIcon');

            const spinnerSVG = `<svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

            function showError(title, message) {
                geoError.classList.remove('hidden');
                geoErrorTitle.textContent = title;
                geoErrorMessage.textContent = message;
            }

            function hideError() {
                geoError.classList.add('hidden');
            }

            function handlePointage(btn, form, latInput, lngInput, textEl, iconEl, originalText, originalIconHTML) {
                if (!navigator.geolocation) {
                    showError('🚫 Navigateur non compatible', 'Votre navigateur ne supporte pas la géolocalisation.');
                    return;
                }

                btn.disabled = true;
                textEl.textContent = 'Recherche de position...';
                iconEl.innerHTML = spinnerSVG;
                hideError();

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        latInput.value = position.coords.latitude;
                        lngInput.value = position.coords.longitude;
                        textEl.textContent = 'Envoi en cours...';
                        form.submit();
                    },
                    function(error) {
                        btn.disabled = false;
                        textEl.textContent = originalText;
                        iconEl.innerHTML = originalIconHTML;

                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                showError('⚠️ Géolocalisation refusée', 'Veuillez autoriser l\'accès à votre position.');
                                break;
                            case error.POSITION_UNAVAILABLE:
                                showError('📍 Position indisponible', 'Impossible de déterminer votre position.');
                                break;
                            case error.TIMEOUT:
                                showError('⏱️ Délai dépassé', 'La recherche de position a pris trop de temps.');
                                break;
                            default:
                                showError('Erreur', 'Une erreur inattendue s\'est produite.');
                        }
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            }

            const checkInOriginalIcon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>`;
            const checkOutOriginalIcon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>`;

            if (checkInBtn) {
                checkInBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    handlePointage(checkInBtn, checkInForm, checkInLat, checkInLng, checkInText, checkInIcon, 'Pointer l\'arrivée', checkInOriginalIcon);
                });
            }

            if (checkOutBtn) {
                checkOutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    handlePointage(checkOutBtn, checkOutForm, checkOutLat, checkOutLng, checkOutText, checkOutIcon, 'Pointer le départ', checkOutOriginalIcon);
                });
            }

            const urgencyCheckOutBtn = document.getElementById('urgencyCheckOutBtn');
            const urgencyCheckOutForm = document.getElementById('urgencyCheckOutForm');
            const urgencyCheckOutLat = document.getElementById('urgencyCheckOutLat');
            const urgencyCheckOutLng = document.getElementById('urgencyCheckOutLng');

            if (urgencyCheckOutBtn) {
                urgencyCheckOutBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    if (!navigator.geolocation) {
                        showError('🚫 Navigateur non compatible', 'Votre navigateur ne supporte pas la géolocalisation.');
                        return;
                    }

                    urgencyCheckOutBtn.disabled = true;
                    urgencyCheckOutBtn.textContent = 'Recherche...';
                    hideError();

                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            urgencyCheckOutLat.value = position.coords.latitude;
                            urgencyCheckOutLng.value = position.coords.longitude;
                            urgencyCheckOutBtn.textContent = 'Envoi...';
                            urgencyCheckOutForm.submit();
                        },
                        function(error) {
                            urgencyCheckOutBtn.disabled = false;
                            urgencyCheckOutBtn.textContent = 'Confirmer';
                            showError('Erreur de géolocalisation', 'Veuillez autoriser l\'accès à votre position.');
                        },
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                    );
                });
            }
        });
    </script>
    @endif
</x-layouts.employee>
