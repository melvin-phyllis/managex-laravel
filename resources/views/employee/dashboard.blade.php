<x-layouts.employee>
    <div class="space-y-6">
        <!-- Flash Messages -->
       

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg animate-fade-in" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-green-800">Succès</h3>
                        <p class="mt-1 text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="ml-3 text-green-400 hover:text-green-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Urgent Announcements Banner -->
        @if(isset($urgentAnnouncements) && $urgentAnnouncements->count() > 0)
            <div class="space-y-3">
                @foreach($urgentAnnouncements as $announcement)
                    <div class="relative overflow-hidden rounded-xl shadow-lg
                        @if($announcement->type === 'urgent' || $announcement->priority === 'critical')
                            bg-gradient-to-r from-red-500 to-rose-600
                        @else
                            bg-gradient-to-r from-amber-500 to-orange-500
                        @endif
                        text-white p-4"
                        x-data="{ dismissed: false }" 
                        x-show="!dismissed"
                        x-transition>
                        
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <span class="text-2xl">{{ $announcement->type_icon }}</span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    @if($announcement->is_pinned)
                                        <span>📌</span>
                                    @endif
                                    <h3 class="font-bold text-lg">{{ $announcement->title }}</h3>
                                    @if($announcement->priority === 'critical')
                                        <span class="px-2 py-0.5 text-xs font-medium bg-white/20 rounded-full">Critique</span>
                                    @endif
                                </div>
                                <p class="text-white/90 text-sm line-clamp-2">
                                    {{ Str::limit(strip_tags($announcement->content), 150) }}
                                </p>
                                <div class="flex items-center gap-4 mt-3">
                                    <a href="{{ route('employee.announcements.show', $announcement) }}" 
                                       class="text-sm font-medium underline hover:no-underline">
                                        Lire la suite →
                                    </a>
                                    @if($announcement->requires_acknowledgment)
                                        <button onclick="acknowledgeAnnouncement({{ $announcement->id }}, this)"
                                                class="px-3 py-1 bg-white text-red-600 text-sm font-medium rounded-lg hover:bg-white/90 transition">
                                            ✓ J'ai pris connaissance
                                        </button>
                                    @endif
                                </div>
                            </div>
                            
                            @if(!$announcement->requires_acknowledgment)
                                <button @click="dismissed = true; markAnnouncementRead({{ $announcement->id }})"
                                        class="text-white/70 hover:text-white p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Header with Streak -->
        <div class="space-y-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bienvenue, {{ auth()->user()->name }}</h1>
                <p class="text-gray-500 mt-1">{{ now()->translatedFormat('l d F Y') }}</p>
            </div>

            <!-- Streak Counter - Full Width -->
            @if($streakData['current'] > 0 || $streakData['best'] > 0)
                <x-streak-counter
                    :currentStreak="$streakData['current']"
                    :bestStreak="$streakData['best']"
                    :lastPresenceDate="$streakData['last_date']" />
            @endif
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
            <x-stat-card
                title="Présences ce mois"
                :value="$stats['presences_month']"
                :subtitle="$stats['heures_month'] . 'h travaillées'"
                color="green"
                :link="route('employee.presences.index')">
                <x-slot:icon>
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Tâches en cours"
                :value="$stats['active_tasks']"
                :subtitle="$stats['tasks_completed'] . ' terminées'"
                color="blue"
                :link="route('employee.tasks.index')">
                <x-slot:icon>
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Congés restants"
                :value="$stats['leave_days_remaining']"
                subtitle="jours disponibles"
                color="purple"
                :link="route('employee.leaves.index')">
                <x-slot:icon>
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Sondages"
                :value="$stats['pending_surveys']"
                subtitle="à compléter"
                color="indigo"
                :link="route('employee.surveys.index')">
                <x-slot:icon>
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </div>

        <!-- Presence Check-in/out & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Presence Check-in/out -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Pointage du jour
                </h2>

                @if($todayPresence)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center space-x-6">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Arrivée</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">{{ $todayPresence->check_in->format('H:i') }}</p>
                            </div>
                            @if($todayPresence->check_out)
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Départ</p>
                                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $todayPresence->check_out->format('H:i') }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Durée</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $todayPresence->hours_worked }}</p>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                        En cours
                                    </span>
                                </div>
                            @endif
                        </div>
                        @if(!$todayPresence->check_out)
                            <form id="dashboardCheckOutForm" action="{{ route('employee.presences.check-out') }}" method="POST">
                                @csrf
                                <input type="hidden" name="latitude" id="dashboardCheckOutLat">
                                <input type="hidden" name="longitude" id="dashboardCheckOutLng">
                                <button type="button" id="dashboardCheckOutBtn"
                                        class="w-full sm:w-auto px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg touch-target">
                                    <svg id="dashboardCheckOutIcon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span id="dashboardCheckOutText">Pointer la sortie</span>
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Pas encore pointé</p>
                                <p class="text-sm text-gray-500">Pointez pour commencer votre journée</p>
                            </div>
                        </div>
                        <form id="dashboardCheckInForm" action="{{ route('employee.presences.check-in') }}" method="POST">
                            @csrf
                            <input type="hidden" name="latitude" id="dashboardCheckInLat">
                            <input type="hidden" name="longitude" id="dashboardCheckInLng">
                            <button type="button" id="dashboardCheckInBtn"
                                    class="w-full sm:w-auto px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg touch-target">
                                <svg id="dashboardCheckInIcon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                <span id="dashboardCheckInText">Pointer l'arrivée</span>
                            </button>
                        </form>
                    </div>

                    <!-- Message d'erreur géolocalisation -->
                    <div id="dashboardGeoError" class="mt-4 hidden bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg animate-fade-in">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800" id="dashboardGeoErrorTitle">Géolocalisation requise</h3>
                                <p class="mt-1 text-sm text-red-700" id="dashboardGeoErrorMessage">Vous devez activer la géolocalisation pour pointer.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <x-quick-actions />
        </div>

        <!-- Monthly Goals -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Objectifs du mois
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                <div class="text-center">
                    <x-circular-progress
                        :value="$monthlyGoals['presence']['current']"
                        :max="$monthlyGoals['presence']['target']"
                        color="auto"
                        size="lg" />
                    <p class="mt-3 font-medium text-gray-900">Présences</p>
                    <p class="text-sm text-gray-500">{{ $monthlyGoals['presence']['current'] }}/{{ $monthlyGoals['presence']['total'] }} jours</p>
                </div>
                <div class="text-center">
                    <x-circular-progress
                        :value="$monthlyGoals['hours']['current']"
                        :max="$monthlyGoals['hours']['expected']"
                        color="auto"
                        size="lg" />
                    <p class="mt-3 font-medium text-gray-900">Heures</p>
                    <p class="text-sm text-gray-500">{{ $monthlyGoals['hours']['current'] }}/{{ $monthlyGoals['hours']['target'] }}h</p>
                </div>
                <div class="text-center">
                    <x-circular-progress
                        :value="$monthlyGoals['tasks']['completed']"
                        :max="max($monthlyGoals['tasks']['assigned'], 1)"
                        color="auto"
                        size="lg" />
                    <p class="mt-3 font-medium text-gray-900">Tâches</p>
                    <p class="text-sm text-gray-500">{{ $monthlyGoals['tasks']['completed'] }}/{{ $monthlyGoals['tasks']['assigned'] }} terminées</p>
                </div>
            </div>
        </div>

        <!-- Documents Quick Access Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- 📋 Mes Demandes de Documents -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow animate-fade-in-up">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-amber-100 rounded-xl">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Mes Demandes</h3>
                        <p class="text-sm text-gray-500">Attestations & certificats</p>
                    </div>
                </div>
                
                @if($documentRequests->count() > 0)
                    <div class="space-y-2 mb-4">
                        @foreach($documentRequests as $request)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50">
                                <div class="flex items-center gap-2">
                                    @if($request->status === 'approved')
                                        <span class="text-green-500">✅</span>
                                    @elseif($request->status === 'rejected')
                                        <span class="text-red-500">❌</span>
                                    @else
                                        <span class="text-amber-500">⏳</span>
                                    @endif
                                    <span class="text-sm text-gray-700 truncate">{{ Str::limit($request->type_label, 18) }}</span>
                                </div>
                                @if($request->hasDocument())
                                    <a href="{{ route('employee.document-requests.download', $request) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded-full 
                                        @if($request->status === 'approved') bg-green-100 text-green-700
                                        @elseif($request->status === 'rejected') bg-red-100 text-red-700
                                        @else bg-amber-100 text-amber-700 @endif">
                                        {{ $request->status_label }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 mb-4">Aucune demande récente</p>
                @endif
                
                <a href="{{ route('employee.document-requests.create') }}" 
                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouvelle demande
                </a>
            </div>

            <!-- 🏢 Documents à Consulter -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow animate-fade-in-up">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Documents Entreprise</h3>
                        <p class="text-sm text-gray-500">Règlements & chartes</p>
                    </div>
                </div>
                
                @if($unreadGlobalDocs->count() > 0)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                        <div class="flex items-center gap-2 text-amber-700">
                            <span class="text-lg">⚠️</span>
                            <span class="text-sm font-medium">{{ $unreadGlobalDocs->count() }} document(s) à consulter</span>
                        </div>
                        <ul class="mt-2 space-y-1">
                            @foreach($unreadGlobalDocs->take(2) as $doc)
                                <li class="text-sm text-amber-600 flex items-center gap-1">
                                    <span>•</span>
                                    <span class="truncate">{{ Str::limit($doc->title, 25) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                        <div class="flex items-center gap-2 text-green-700">
                            <span class="text-lg">✅</span>
                            <span class="text-sm font-medium">Tous les documents lus</span>
                        </div>
                    </div>
                @endif
                
                <a href="{{ route('employee.documents.index') }}" 
                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                    Consulter →
                </a>
            </div>

            <!-- 📄 Mon Contrat -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow animate-fade-in-up">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Mon Contrat</h3>
                        <p class="text-sm text-gray-500">Document de travail</p>
                    </div>
                </div>
                
                @if($contract)
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Type</span>
                            <span class="font-medium text-gray-900">{{ $contract->type ?? 'CDI' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Depuis</span>
                            <span class="font-medium text-gray-900">{{ $contract->start_date?->format('d/m/Y') ?? '-' }}</span>
                        </div>
                    </div>
                    
                    @if($hasContractDocument)
                        <a href="{{ route('employee.documents.download-contract') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-500 text-white text-sm font-medium rounded-lg hover:bg-purple-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Télécharger
                        </a>
                    @else
                        <div class="text-center py-2 text-sm text-gray-400">
                            Document non disponible
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <span class="text-3xl">📋</span>
                        <p class="text-sm text-gray-400 mt-2">Aucun contrat trouvé</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Weekly Hours Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-fade-in-up">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Heures cette semaine</h3>
                <canvas id="weeklyHoursChart" height="200"></canvas>
            </div>

            <!-- Tasks Distribution Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-fade-in-up">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mes tâches par statut</h3>
                <canvas id="tasksChart" height="200"></canvas>
            </div>
        </div>

        <!-- Tasks & Events Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Tasks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fade-in-up">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Mes tâches en cours</h3>
                    <a href="{{ route('employee.tasks.index') }}" class="text-sm text-green-600 hover:text-green-800 font-medium">Voir tout</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentTasks as $index => $task)
                        <a href="{{ route('employee.tasks.show', $task) }}"
                           class="block p-4 hover:bg-gray-50 transition-colors"
                           style="animation-delay: {{ $index * 50 }}ms">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-900">{{ Str::limit($task->titre, 30) }}</span>
                                <x-status-badge :status="$task->priorite" type="priority" />
                            </div>
                            <x-progress-bar :value="$task->progression" size="sm" />
                            @if($task->date_fin)
                                <p class="text-xs text-gray-400 mt-2">
                                    Échéance: {{ $task->date_fin->format('d/m/Y') }}
                                    @if($task->date_fin->isPast())
                                        <span class="text-red-500 font-medium">(En retard)</span>
                                    @elseif($task->date_fin->isToday())
                                        <span class="text-yellow-500 font-medium">(Aujourd'hui)</span>
                                    @endif
                                </p>
                            @endif
                        </a>
                    @empty
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-500">Aucune tâche en cours</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Events -->
            <x-upcoming-events :events="$upcomingEvents" :maxItems="5" />
        </div>

        <!-- Surveys & Notifications Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pending Surveys -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fade-in-up">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Sondages à compléter</h3>
                    <a href="{{ route('employee.surveys.index') }}" class="text-sm text-green-600 hover:text-green-800 font-medium">Voir tout</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($pendingSurveys as $survey)
                        <a href="{{ route('employee.surveys.show', $survey) }}" class="block p-4 hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $survey->titre }}</p>
                                        <p class="text-sm text-gray-500">{{ $survey->questions->count() }} question(s)</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($survey->date_limite)
                                        <span class="text-xs px-2 py-1 rounded-full {{ $survey->date_limite->isPast() ? 'bg-red-100 text-red-600' : ($survey->date_limite->diffInDays(now()) <= 3 ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-600') }}">
                                            {{ $survey->date_limite->format('d/m') }}
                                        </span>
                                    @endif
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 mt-1 ml-auto transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-500">Tous les sondages sont complétés</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fade-in-up">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Notifications récentes</h3>
                    @if($recentNotifications->count() > 0)
                        <form action="{{ route('employee.notifications.read-all') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Tout marquer comme lu</button>
                        </form>
                    @endif
                </div>
                <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto scrollbar-thin">
                    @forelse($recentNotifications as $notification)
                        <div class="p-4 flex items-start {{ $notification->read_at ? 'opacity-60' : '' }} hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full {{ $notification->read_at ? 'bg-gray-300' : 'bg-green-500 animate-pulse' }}"></div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-900">{{ $notification->data['message'] ?? 'Nouvelle notification' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notification->read_at)
                                <form action="{{ route('employee.notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <p class="text-gray-500">Aucune notification</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Chart Data
        const chartData = @json($chartData);

        // Weekly Hours Chart
        new Chart(document.getElementById('weeklyHoursChart'), {
            type: 'bar',
            data: {
                labels: chartData.weekly_hours.labels,
                datasets: [{
                    label: 'Heures',
                    data: chartData.weekly_hours.data,
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderRadius: 6,
                    borderSkipped: false,
                    hoverBackgroundColor: '#22C55E'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + 'h travaillées';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            callback: function(value) {
                                return value + 'h';
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // Tasks Distribution Chart
        new Chart(document.getElementById('tasksChart'), {
            type: 'doughnut',
            data: {
                labels: chartData.tasks_by_status.labels,
                datasets: [{
                    data: chartData.tasks_by_status.data,
                    backgroundColor: chartData.tasks_by_status.colors,
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            }
        });

        // Geolocation handling
        document.addEventListener('DOMContentLoaded', function() {
            const geoError = document.getElementById('dashboardGeoError');
            const geoErrorTitle = document.getElementById('dashboardGeoErrorTitle');
            const geoErrorMessage = document.getElementById('dashboardGeoErrorMessage');

            const checkInBtn = document.getElementById('dashboardCheckInBtn');
            const checkInForm = document.getElementById('dashboardCheckInForm');
            const checkInLat = document.getElementById('dashboardCheckInLat');
            const checkInLng = document.getElementById('dashboardCheckInLng');
            const checkInText = document.getElementById('dashboardCheckInText');
            const checkInIcon = document.getElementById('dashboardCheckInIcon');

            const checkOutBtn = document.getElementById('dashboardCheckOutBtn');
            const checkOutForm = document.getElementById('dashboardCheckOutForm');
            const checkOutLat = document.getElementById('dashboardCheckOutLat');
            const checkOutLng = document.getElementById('dashboardCheckOutLng');
            const checkOutText = document.getElementById('dashboardCheckOutText');
            const checkOutIcon = document.getElementById('dashboardCheckOutIcon');

            const spinnerSVG = `<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>`;

            function showError(title, message) {
                if (geoError) {
                    geoError.classList.remove('hidden');
                    geoErrorTitle.textContent = title;
                    geoErrorMessage.textContent = message;
                }
            }

            function hideError() {
                if (geoError) geoError.classList.add('hidden');
            }

            function handlePointage(btn, form, latInput, lngInput, textEl, iconEl, originalText, originalIconHTML) {
                if (!navigator.geolocation) {
                    showError('Navigateur non compatible', 'Votre navigateur ne supporte pas la géolocalisation.');
                    return;
                }

                btn.disabled = true;
                textEl.textContent = 'Localisation...';
                iconEl.outerHTML = spinnerSVG;
                hideError();

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        latInput.value = position.coords.latitude;
                        lngInput.value = position.coords.longitude;
                        textEl.textContent = 'Envoi...';
                        form.submit();
                    },
                    function(error) {
                        btn.disabled = false;
                        textEl.textContent = originalText;
                        const newIcon = document.createElement('svg');
                        newIcon.id = iconEl.id;
                        newIcon.className = 'w-5 h-5 mr-2';
                        newIcon.setAttribute('fill', 'none');
                        newIcon.setAttribute('stroke', 'currentColor');
                        newIcon.setAttribute('viewBox', '0 0 24 24');
                        newIcon.innerHTML = originalIconHTML;
                        btn.insertBefore(newIcon, btn.firstChild);

                        const messages = {
                            1: ['Géolocalisation refusée', 'Veuillez autoriser l\'accès à votre position.'],
                            2: ['Position indisponible', 'Vérifiez que le GPS est activé.'],
                            3: ['Délai dépassé', 'La recherche de position a pris trop de temps.']
                        };
                        const [title, msg] = messages[error.code] || ['Erreur', 'Une erreur s\'est produite.'];
                        showError(title, msg);
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            }

            const checkInOriginalIcon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>`;
            const checkOutOriginalIcon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>`;

            if (checkInBtn) {
                checkInBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    handlePointage(checkInBtn, checkInForm, checkInLat, checkInLng, checkInText, checkInIcon, 'Pointer l\'arrivée', checkInOriginalIcon);
                });
            }

            if (checkOutBtn) {
                checkOutBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    handlePointage(checkOutBtn, checkOutForm, checkOutLat, checkOutLng, checkOutText, checkOutIcon, 'Pointer la sortie', checkOutOriginalIcon);
                });
            }
        });

        // Announcement functions
        function markAnnouncementRead(id) {
            fetch(`/employee/announcements/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
        }

        function acknowledgeAnnouncement(id, button) {
            button.disabled = true;
            button.textContent = 'Envoi...';

            fetch(`/employee/announcements/${id}/acknowledge`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.textContent = '✓ Confirmé';
                    button.classList.remove('bg-white', 'text-red-600');
                    button.classList.add('bg-green-500', 'text-white');
                    setTimeout(() => {
                        button.closest('.rounded-xl').remove();
                    }, 1500);
                }
            })
            .catch(() => {
                button.textContent = 'Erreur';
                button.disabled = false;
            });
        }
    </script>
    @endpush
</x-layouts.employee>
