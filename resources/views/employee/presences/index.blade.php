<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header avec horloge en temps réel -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-fade-in-up">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mes présences</h1>
                <p class="text-sm text-gray-500 mt-1">Suivez votre temps de travail et votre ponctualité</p>
            </div>
            <!-- Horloge en temps réel -->
            <div class="text-white px-6 py-4 rounded-2xl shadow-lg" style="background: linear-gradient(135deg, #31708E, #5085A5);" x-data="{ time: '' }" x-init="
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
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-sm border border-gray-200 p-4 animate-fade-in-up animation-delay-100">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="p-2.5 rounded-xl" style="background: rgba(49, 112, 142, 0.15);">
                            <svg class="w-5 h-5" style="color: #31708E;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Horaires</p>
                            <p class="font-semibold text-gray-900">{{ $workSettings['work_start'] }} - {{ $workSettings['work_end'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="p-2.5 rounded-xl" style="background: rgba(80, 133, 165, 0.15);">
                            <svg class="w-5 h-5" style="color: #5085A5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Pause</p>
                            <p class="font-semibold text-gray-900">{{ $workSettings['break_start'] }} - {{ $workSettings['break_end'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="p-2.5 rounded-xl" style="background: rgba(143, 193, 227, 0.2);">
                            <svg class="w-5 h-5" style="color: #5085A5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tolérance retard</p>
                            <p class="font-semibold text-gray-900">{{ $workSettings['late_tolerance'] }} min</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2" x-data="{ showWorkDaysEditor: false }">
                    <span class="text-sm text-gray-600">Vos jours de travail:</span>
                    <span class="font-medium text-indigo-700">{{ $workDayNames ?? 'Non définis' }}</span>
                    @if(isset($modificationsThisWeek) && $modificationsThisWeek < $maxModifications)
                    <button @click="showWorkDaysEditor = !showWorkDaysEditor" type="button" class="ml-2 inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Modifier
                    </button>
                    @else
                    <span class="ml-2 inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        2/2 modifications
                    </span>
                    @endif

                    <!-- Work Days Editor Panel (collapsible) -->
                     <div x-show="showWorkDaysEditor" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                          class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50" @click.self="showWorkDaysEditor = false" @keydown.escape.window="showWorkDaysEditor = false">
                         <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6" @click.stop
                              x-data="{
                                  selectedDays: @json($currentWorkDays ?? []),
                                  maxAllowed: {{ $maxAllowedDays ?? 3 }},
                                  dayNames: @json([1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi']),
                                  dayShort: @json([1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 4 => 'Jeu', 5 => 'Ven']),
                                  dayIcons: @json([1 => '🔵', 2 => '🟢', 3 => '🟡', 4 => '🟠', 5 => '🔴']),
                                  toggleDay(day) {
                                      const idx = this.selectedDays.indexOf(day);
                                      if (idx > -1) { this.selectedDays.splice(idx, 1); }
                                      else if (this.selectedDays.length < this.maxAllowed) { this.selectedDays.push(day); }
                                  },
                                  isSelected(day) { return this.selectedDays.includes(day); },
                                  get isMaxReached() { return this.selectedDays.length >= this.maxAllowed; },
                                  get isValid() { return this.selectedDays.length >= 1 && this.selectedDays.length <= this.maxAllowed; }
                              }">
                             <div class="flex items-center justify-between mb-5">
                                 <div>
                                     <h3 class="text-lg font-semibold text-gray-900">Modifier vos jours de travail</h3>
                                     <p class="text-xs text-gray-500 mt-0.5">Sélectionnez au maximum <span class="font-bold text-indigo-600" x-text="maxAllowed"></span> jour(s) cette semaine</p>
                                 </div>
                                 <button @click="showWorkDaysEditor = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100">
                                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                 </button>
                            </div>

                            <div class="grid grid-cols-5 gap-2 mb-5">
                                <template x-for="day in [1,2,3,4,5]" :key="day">
                                    <button type="button" @click="toggleDay(day)"
                                            :class="isSelected(day)
                                                ? 'bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/30 scale-105 border-transparent'
                                                : (isMaxReached ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-gray-50 text-gray-600 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50')"
                                            :disabled="!isSelected(day) && isMaxReached"
                                            class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition-all duration-200 transform">
                                        <span class="text-lg" x-text="dayIcons[day]"></span>
                                        <span class="text-xs font-semibold" x-text="dayShort[day]"></span>
                                    </button>
                                </template>
                            </div>

                            <!-- Validation + Counter -->
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span class="text-xs font-medium" :class="isValid ? 'text-green-600' : 'text-red-500'"
                                          x-text="selectedDays.length + '/' + maxAllowed + ' jour(s) sélectionné(s)' + (isMaxReached ? ' (max atteint)' : '')"></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs text-gray-500">Modifications cette semaine:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $modificationsThisWeek >= $maxModifications ? 'bg-red-100 text-red-700' : 'bg-indigo-100 text-indigo-700' }}">{{ $modificationsThisWeek }}/{{ $maxModifications }}</span>
                                </div>
                            </div>

                            <form action="{{ route('employee.presences.work-days') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <template x-for="day in selectedDays" :key="'input-' + day">
                                    <input type="hidden" name="work_days[]" :value="day">
                                </template>
                                <div class="flex gap-3">
                                    <button type="button" @click="showWorkDaysEditor = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors">Annuler</button>
                                    <button type="submit" :disabled="!isValid"
                                            class="flex-1 px-4 py-2.5 text-white rounded-xl font-medium transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
                                            style="background: linear-gradient(135deg, #31708E, #5085A5);">
                                        Enregistrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Avertissement retards expirants -->
        @if(isset($expiringLateData) && ($expiringLateData['expiring_minutes'] > 0 || count($expiringLateData['upcoming_penalties']) > 0))
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-4 animate-fade-in-up">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 bg-red-100 p-2 rounded-lg">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-red-800">Attention - Heures de retard à rattraper</h4>
                    
                    @if($expiringLateData['expiring_minutes'] > 0)
                        @php
                            $expMins = $expiringLateData['expiring_minutes'];
                            $expHours = floor($expMins / 60);
                            $expMinsRemainder = $expMins % 60;
                            $expFormatted = $expHours > 0 ? "{$expHours}h" . ($expMinsRemainder > 0 ? sprintf('%02d', $expMinsRemainder) : '') : "{$expMinsRemainder} min";
                        @endphp
                        <p class="text-sm text-red-700 mt-1">
                            <strong>{{ $expFormatted }}</strong> de retard expirent dans les prochains jours. 
                            Rattrapez-les en restant plus tard !
                        </p>
                        
                        @if($expiringLateData['expiring_presences']->isNotEmpty())
                            <div class="mt-2 space-y-1">
                                @foreach($expiringLateData['expiring_presences']->take(3) as $expPresence)
                                    <div class="flex items-center gap-2 text-xs text-red-600">
                                        <span class="font-medium">{{ $expPresence->date->format('d/m') }}</span>
                                        <span>à</span>
                                        <span>{{ $expPresence->unrecovered_minutes }} min</span>
                                        <span class="text-red-500">
                                            (expire {{ $expPresence->late_recovery_deadline?->format('d/m') }})
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif

                    @if(count($expiringLateData['upcoming_penalties']) > 0)
                        <div class="mt-3 pt-3 border-t border-red-200">
                            <p class="text-sm font-semibold text-red-800">⚠️ Absences pénalité programmées :</p>
                            @foreach($expiringLateData['upcoming_penalties'] as $penalty)
                                <div class="flex items-center gap-2 text-sm text-red-700 mt-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <strong>{{ $penalty->absence_date->format('d/m/Y') }}</strong> - 
                                    Absence due à {{ $penalty->formatted_expired_time }} de retard non rattrapé
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @php
                        $thresholdMins = $expiringLateData['penalty_threshold'];
                        $expiredMins = $expiringLateData['expired_minutes'];
                        $thresholdHours = $thresholdMins / 60;
                        $progressPercent = $thresholdMins > 0 ? min(100, ($expiredMins / $thresholdMins) * 100) : 0;
                    @endphp
                    @if($expiredMins > 0)
                        <div class="mt-3 pt-3 border-t border-red-200">
                            <p class="text-xs text-red-600 mb-1">
                                Progression vers prochaine absence pénalité (seuil: {{ $thresholdHours }}h)
                            </p>
                            <div class="w-full bg-red-200 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
                            </div>
                            <p class="text-xs text-red-500 mt-1">
                                {{ floor($expiredMins / 60) }}h{{ $expiredMins % 60 > 0 ? sprintf('%02d', $expiredMins % 60) : '' }} / {{ $thresholdHours }}h
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Jour non travaillé - Option de rattrapage -->
        @if(isset($isWorkingDay) && !$isWorkingDay)
            @if(isset($canStartRecoverySession) && $canStartRecoverySession && isset($recoverySessionInfo))
                <!-- Session de rattrapage disponible -->
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 border border-violet-200 rounded-2xl p-5 mb-4 animate-fade-in-up">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 bg-gradient-to-br from-violet-500 to-purple-600 p-3 rounded-xl shadow-lg shadow-violet-500/30">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-violet-900">Session de rattrapage disponible</h3>
                                <p class="text-sm text-violet-700 mt-1">
                                    Aujourd'hui n'est pas un jour de travail, mais vous avez 
                                    <strong class="text-violet-900">{{ $recoverySessionInfo['formatted'] }}</strong> de retard à rattraper.
                                </p>
                                <p class="text-xs text-violet-600 mt-2">
                                     Vous pouvez venir travailler aujourd'hui pour rattraper vos heures. Tout le temps travaillé sera comptabilisé comme rattrapage.
                                </p>
                            </div>
                        </div>
                        <form id="recoveryStartForm" action="{{ route('employee.presences.recovery.start') }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="latitude" id="recoveryStartLat">
                            <input type="hidden" name="longitude" id="recoveryStartLng">
                            <button type="button" id="recoveryStartBtn"
                                    class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 text-white font-medium rounded-xl hover:from-violet-700 hover:to-purple-700 transition-all shadow-lg shadow-violet-500/30 flex items-center justify-center gap-2">
                                <svg id="recoveryStartIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="recoveryStartText">Démarrer le rattrapage</span>
                            </button>
                        </form>
                    </div>
                </div>
            @elseif(!isset($todayPresence) || !$todayPresence)
                <!-- Jour non travaillé sans heures à rattraper -->
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700">
                                <strong>Aujourd'hui n'est pas un jour de travail pour vous.</strong> Le pointage normal est désactivé.
                            </p>
                            @if(isset($totalUnrecoveredMinutes) && $totalUnrecoveredMinutes <= 0)
                                <p class="text-xs text-amber-600 mt-1">Vous n'avez pas d'heures de retard à rattraper. Profitez de votre repos !</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
        
        <!-- Session de rattrapage en cours -->
        @if(isset($isRecoverySessionToday) && $isRecoverySessionToday && $todayPresence && !$todayPresence->check_out)
        <div class="bg-gradient-to-r from-violet-100 to-purple-100 border-2 border-violet-300 rounded-2xl p-5 mb-4 animate-fade-in-up">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 bg-gradient-to-br from-violet-600 to-purple-700 p-3 rounded-xl shadow-lg shadow-violet-500/40">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-violet-900 flex items-center gap-2">
                            <span class="w-2 h-2 bg-violet-500 rounded-full animate-pulse"></span>
                            Session de rattrapage en cours
                        </h3>
                        <p class="text-sm text-violet-700 mt-1">
                            Arrivée à <strong>{{ $todayPresence->check_in->format('H:i') }}</strong>
                            <span class="mx-2">•</span>
                            En cours depuis <span id="recoveryDuration" class="font-medium">--</span>
                        </p>
                        <p class="text-xs text-violet-600 mt-2">
                            Tout le temps travaillé aujourd'hui sera comptabilisé comme rattrapage de vos heures de retard.
                        </p>
                    </div>
                </div>
                <form id="recoveryEndForm" action="{{ route('employee.presences.recovery.end') }}" method="POST" class="flex-shrink-0">
                    @csrf
                    <input type="hidden" name="latitude" id="recoveryEndLat">
                    <input type="hidden" name="longitude" id="recoveryEndLng">
                    <button type="button" id="recoveryEndBtn"
                            class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white font-medium rounded-xl hover:from-red-600 hover:to-rose-700 transition-all shadow-lg shadow-red-500/30 flex items-center justify-center gap-2">
                        <svg id="recoveryEndIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                        </svg>
                        <span id="recoveryEndText">Terminer le rattrapage</span>
                    </button>
                </form>
            </div>
        </div>
        <script nonce="{{ $cspNonce ?? '' }}">
            // Afficher la durée de la session de rattrapage
            (function() {
                const checkInTime = new Date('{{ $todayPresence->check_in->toIso8601String() }}');
                const durationEl = document.getElementById('recoveryDuration');
                
                function updateDuration() {
                    const now = new Date();
                    const diffMs = now - checkInTime;
                    const diffMins = Math.floor(diffMs / 60000);
                    const hours = Math.floor(diffMins / 60);
                    const mins = diffMins % 60;
                    
                    if (hours > 0) {
                        durationEl.textContent = hours + 'h' + (mins < 10 ? '0' : '') + mins;
                    } else {
                        durationEl.textContent = mins + ' min';
                    }
                }
                
                updateDuration();
                setInterval(updateDuration, 60000);
            })();
        </script>
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

        <!-- Avertissement Aprés 17h -->
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
                @if(!$todayPresence || ($preCheckIn && !$preCheckIn->check_in))
                    @if($preCheckIn && !$preCheckIn->check_in)
                        {{-- Pré-pointage en cours - Compteur à rebours --}}
                        <div class="flex-1" x-data="{
                            countdown: '',
                            canConfirm: {{ $isBeforeWorkStart ? 'false' : 'true' }},
                            init() {
                                const workStart = '{{ $workSettings['work_start'] }}';
                                const [h, m] = workStart.split(':').map(Number);
                                const target = new Date();
                                target.setHours(h, m, 0, 0);
                                
                                const update = () => {
                                    const now = new Date();
                                    const diff = target - now;
                                    if (diff <= 0) {
                                        this.countdown = '00:00';
                                        this.canConfirm = true;
                                        // Play notification sound
                                        this.playSound();
                                        return;
                                    }
                                    const hours = Math.floor(diff / 3600000);
                                    const mins = Math.floor((diff % 3600000) / 60000);
                                    const secs = Math.floor((diff % 60000) / 1000);
                                    this.countdown = (hours > 0 ? hours + 'h' : '') + 
                                        String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                                };
                                update();
                                setInterval(update, 1000);
                            },
                            playSound() {
                                try {
                                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                                    const notes = [523.25, 659.25, 783.99, 1046.50];
                                    notes.forEach((freq, i) => {
                                        const osc = ctx.createOscillator();
                                        const gain = ctx.createGain();
                                        osc.connect(gain);
                                        gain.connect(ctx.destination);
                                        osc.frequency.value = freq;
                                        osc.type = 'sine';
                                        gain.gain.setValueAtTime(0.3, ctx.currentTime + i * 0.15);
                                        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + i * 0.15 + 0.3);
                                        osc.start(ctx.currentTime + i * 0.15);
                                        osc.stop(ctx.currentTime + i * 0.15 + 0.3);
                                    });
                                } catch(e) {}
                            }
                        }">
                            {{-- Banner d'arrivée anticipée --}}
                            <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-blue-50 border border-indigo-200 rounded-xl p-5 mb-3">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-indigo-900">🌅 Arrivée anticipée enregistrée</p>
                                        <p class="text-sm text-indigo-600">
                                            Arrivé(e) à {{ $preCheckIn->pre_check_in->format('H:i') }} — 
                                            En attente de {{ $workSettings['work_start'] }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Countdown --}}
                                <div class="text-center py-4" x-show="!canConfirm">
                                    <p class="text-sm text-gray-500 mb-2">Votre présence sera confirmée dans</p>
                                    <div class="text-4xl font-bold text-indigo-700 font-mono tracking-wider" x-text="countdown"></div>
                                    <p class="text-xs text-gray-400 mt-2">Une notification vous sera envoyée à {{ $workSettings['work_start'] }}</p>
                                </div>

                                {{-- Bouton confirmer (visible à l'heure) --}}
                                <div x-show="canConfirm" class="mt-2">
                                    <form action="{{ route('employee.presences.check-in') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="latitude" value="{{ $preCheckIn->pre_check_in_latitude }}">
                                        <input type="hidden" name="longitude" value="{{ $preCheckIn->pre_check_in_longitude }}">
                                        <button type="submit" 
                                                class="w-full px-6 py-4 text-white font-medium rounded-xl transition-all shadow-lg flex items-center justify-center gap-3 transform hover:scale-[1.02] active:scale-[0.98] animate-pulse" 
                                                style="background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 10px 15px -3px rgba(5, 150, 105, 0.3);">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-lg">✅ Confirmer ma présence maintenant</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @elseif($canPreCheckIn)
                        {{-- Bouton Pré-pointage (avant l'heure de début) --}}
                        <div class="flex-1">
                            <div class="mb-3 bg-indigo-50 border-l-4 border-indigo-400 p-3 rounded-r-lg">
                                <p class="text-sm text-indigo-700">
                                    <strong>⏰ Arrivée anticipée :</strong> Il est {{ now()->format('H:i') }}, le pointage officiel commence à {{ $workSettings['work_start'] }}.
                                    Vous pouvez signaler votre arrivée maintenant !
                                </p>
                            </div>
                            <form id="preCheckInForm" action="{{ route('employee.presences.pre-check-in') }}" method="POST">
                                @csrf
                                <input type="hidden" name="latitude" id="preCheckInLat">
                                <input type="hidden" name="longitude" id="preCheckInLng">
                                <button type="button" id="preCheckInBtn"
                                        class="w-full px-6 py-4 text-white font-medium rounded-xl transition-all shadow-lg flex items-center justify-center gap-3 transform hover:scale-[1.02] active:scale-[0.98]" 
                                        style="background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-lg">🌅 Je suis arrivé(e) — Pré-pointage</span>
                                </button>
                            </form>

                            {{-- Ou pointer normalement quand l'heure sera arrivée --}}
                            <p class="text-xs text-center text-gray-400 mt-2">
                                Ou attendez {{ $workSettings['work_start'] }} pour pointer normalement
                            </p>
                        </div>
                    @else
                        {{-- Formulaire d'arrivée normal --}}
                        <form id="checkInForm" action="{{ route('employee.presences.check-in') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="latitude" id="checkInLat">
                            <input type="hidden" name="longitude" id="checkInLng">
                            <button type="button" id="checkInBtn" 
                                    {{ isset($canCheckIn) && !$canCheckIn ? 'disabled' : '' }}
                                    class="w-full px-6 py-4 text-white font-medium rounded-xl transition-all shadow-lg flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none transform hover:scale-[1.02] active:scale-[0.98] disabled:hover:scale-100" style="background: linear-gradient(135deg, #31708E, #5085A5); box-shadow: 0 10px 15px -3px rgba(49, 112, 142, 0.3);">
                                <svg id="checkInIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                <span id="checkInText" class="text-lg">
                                    {{ (isset($canCheckIn) && !$canCheckIn) ? 'Pointage non disponible' : "Pointer l'arrivée" }}
                                </span>
                            </button>
                        </form>
                    @endif
                @elseif(!$todayPresence->check_out)
                    <!-- Arrivée pointée + Timer -->
                    <div class="flex-1 px-6 py-4 {{ $todayPresence->is_late ? 'bg-gradient-to-r from-orange-50 to-amber-50 border-orange-200' : 'bg-gradient-to-r from-green-50 to-emerald-50 border-green-200' }} border rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold {{ $todayPresence->is_late ? 'text-orange-800' : 'text-green-800' }}">
                                     Arrivée pointée à {{ $todayPresence->check_in->format('H:i') }}
                                </p>
                                @if($todayPresence->is_late)
                                    <p class="text-sm text-orange-600 mt-1">Retard de {{ abs($todayPresence->late_minutes) }} minutes</p>
                                @endif
                                @if($todayPresence->check_in_status === 'in_zone')
                                    <p class="text-sm text-green-600 mt-1"> Dans la zone autorisée</p>
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
                                <span>Progression journaliére</span>
                                <span x-text="progress + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-1000" :style="'width: ' + progress + '%'"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaires de départ -->
                    <div class="flex-1 space-y-2" x-data="{
                        showUrgencyModal: false,
                        urgencyReason: '',
                        isAfterEndTime: false,
                        hasLateBalance: {{ $totalUnrecoveredMinutes > 0 ? 'true' : 'false' }},
                        lateMinutes: {{ $totalUnrecoveredMinutes }},
                        recoveryMode: false
                    }" x-init="
                        const endParts = '{{ $workSettings['work_end'] }}'.split(':');
                        const checkEnd = () => {
                            const now = new Date();
                            const end = new Date();
                            end.setHours(parseInt(endParts[0]), parseInt(endParts[1]), 0);
                            isAfterEndTime = now >= end;
                        };
                        checkEnd();
                        setInterval(checkEnd, 30000);
                    ">
                        <!-- === APRÈS L'HEURE DE FIN + RETARD À RATTRAPER === -->
                        <template x-if="isAfterEndTime && hasLateBalance && !recoveryMode">
                            <div class="space-y-2">
                                <!-- Bouton principal : Rattraper -->
                                <button type="button" @click="recoveryMode = true" class="w-full px-6 py-4 text-white font-medium rounded-xl transition-all shadow-lg flex items-center justify-center gap-3 transform hover:scale-[1.02] active:scale-[0.98]" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); box-shadow: 0 10px 15px -3px rgba(124, 58, 237, 0.3);">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-lg">Rattraper les heures de retard</span>
                                </button>
                                <p class="text-xs text-center text-violet-600"><span x-text="lateMinutes"></span> min de retard à rattraper</p>

                                <!-- Bouton secondaire : Partir sans rattraper -->
                                <form id="checkOutForm" action="{{ route('employee.presences.check-out') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="latitude" id="checkOutLat">
                                    <input type="hidden" name="longitude" id="checkOutLng">
                                    <button type="button" id="checkOutBtn" class="w-full px-4 py-2.5 text-gray-600 font-medium rounded-xl transition-all flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 text-sm">
                                        <svg id="checkOutIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <span id="checkOutText">Partir sans rattraper</span>
                                    </button>
                                </form>
                            </div>
                        </template>

                        <!-- === MODE RATTRAPAGE ACTIVÉ === -->
                        <template x-if="recoveryMode">
                            <div class="space-y-2">
                                <div class="px-4 py-3 bg-violet-50 border border-violet-200 rounded-xl">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-violet-500 rounded-full animate-pulse"></div>
                                        <p class="text-sm font-semibold text-violet-800">Rattrapage en cours</p>
                                    </div>
                                    <p class="text-xs text-violet-600 mt-1">Vous rattrapez vos <span x-text="lateMinutes"></span> min de retard. Quand vous avez terminé, cliquez sur le bouton ci-dessous pour pointer votre départ.</p>
                                </div>

                                <!-- Bouton principal : Terminer et partir -->
                                <form id="checkOutForm" action="{{ route('employee.presences.check-out') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="latitude" id="checkOutLat">
                                    <input type="hidden" name="longitude" id="checkOutLng">
                                    <input type="hidden" name="is_recovery_session" value="1">
                                    <button type="button" id="checkOutBtn" class="w-full px-6 py-4 text-white font-medium rounded-xl transition-all shadow-lg flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] active:scale-[0.98]" style="background: linear-gradient(135deg, #7c3aed, #5b21b6); box-shadow: 0 10px 15px -3px rgba(124, 58, 237, 0.3);">
                                        <svg id="checkOutIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span id="checkOutText" class="text-lg">Terminer le rattrapage et partir</span>
                                    </button>
                                </form>

                                <button type="button" @click="recoveryMode = false" class="w-full px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 transition-colors text-center">Annuler le rattrapage</button>
                            </div>
                        </template>

                        <!-- === BOUTON DÉPART NORMAL (avant l'heure de fin OU pas de retard) === -->
                        <template x-if="!recoveryMode && !(isAfterEndTime && hasLateBalance)">
                            <form id="checkOutForm" action="{{ route('employee.presences.check-out') }}" method="POST">
                                @csrf
                                <input type="hidden" name="latitude" id="checkOutLat">
                                <input type="hidden" name="longitude" id="checkOutLng">
                                <button type="button" id="checkOutBtn" class="w-full px-6 py-4 text-white font-medium rounded-xl transition-all shadow-lg flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] active:scale-[0.98]" style="background: linear-gradient(135deg, #687864, #5085A5); box-shadow: 0 10px 15px -3px rgba(104, 120, 100, 0.3);">
                                    <svg id="checkOutIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span id="checkOutText" class="text-lg">Pointer le départ</span>
                                </button>
                            </form>
                        </template>

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
                                <p class="font-semibold text-gray-800"> Journée terminée</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $todayPresence->check_in->format('H:i') }} à {{ $todayPresence->check_out->format('H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Durée totale</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $todayPresence->hours_worked }}h</p>
                            </div>
                        </div>
                        @if($todayPresence->is_late || $todayPresence->is_early_departure || $todayPresence->recovery_minutes > 0)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($todayPresence->is_late)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700">Retard: {{ $todayPresence->late_minutes }} min</span>
                            @endif
                            @if($todayPresence->is_early_departure)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">⏰ Départ anticipé: {{ $todayPresence->early_departure_minutes }} min</span>
                            @endif
                            @if($todayPresence->overtime_minutes > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">⭐ Heures sup: {{ $todayPresence->overtime_minutes }} min</span>
                            @endif
                            @if($todayPresence->recovery_minutes > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-700"> Rattrapé: {{ $todayPresence->recovery_minutes }} min</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 flex items-center justify-center">
                        <div class="text-center py-8">
                            <div class="text-5xl mb-2">👋</div>
                            <p class="text-gray-600 font-medium">Bonne fin de journée !</p>
                            <p class="text-sm text-gray-400"> demain</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Stats Cards améliorées -->
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Jours pointés -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="p-2.5 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #31708E, #5085A5); box-shadow: 0 10px 15px -3px rgba(49, 112, 142, 0.3);">
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
                    <div class="p-2.5 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #5085A5, #8FC1E3); box-shadow: 0 10px 15px -3px rgba(80, 133, 165, 0.3);">
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
                    <div class="p-2.5 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #687864, #5085A5); box-shadow: 0 10px 15px -3px rgba(104, 120, 100, 0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold mt-3" style="color: {{ $monthlyStats['punctuality_score'] >= 80 ? '#31708E' : ($monthlyStats['punctuality_score'] >= 60 ? '#5085A5' : '#687864') }};">{{ $monthlyStats['punctuality_score'] }}%</p>
                <p class="text-xs text-gray-500">Ponctualité</p>
            </div>

            <!-- Heures supplémentaires -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="p-2.5 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #8FC1E3, #5085A5); box-shadow: 0 10px 15px -3px rgba(143, 193, 227, 0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold mt-3" style="color: #5085A5;">{{ $monthlyStats['overtime_hours'] }}h</p>
                <p class="text-xs text-gray-500">Heures sup.</p>
            </div>

            <!-- Retards -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="p-2.5 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #687864, #31708E); box-shadow: 0 10px 15px -3px rgba(104, 120, 100, 0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold mt-3" style="color: #687864;">{{ $monthlyStats['total_late'] }}</p>
                <p class="text-xs text-gray-500">Retards ({{ $monthlyStats['total_late_minutes'] }} min)</p>
            </div>

            <!-- Solde Rattrapage -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    @php
                        $balanceStatus = $recoveryStats['status'] ?? 'ok';
                        $balanceColors = [
                            'deficit' => 'from-red-500 to-rose-600 shadow-red-500/30',
                            'warning' => 'from-amber-500 to-orange-600 shadow-amber-500/30',
                            'ok' => 'from-emerald-500 to-green-600 shadow-emerald-500/30',
                        ];
                        $balanceClass = $balanceColors[$balanceStatus] ?? $balanceColors['ok'];
                    @endphp
                    <div class="bg-gradient-to-br {{ $balanceClass }} p-2.5 rounded-xl shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                </div>
                @php
                    $balance = $recoveryStats['total_balance'] ?? 0;
                    $absBalance = abs($balance);
                    $hours = floor($absBalance / 60);
                    $mins = $absBalance % 60;
                    $balanceFormatted = $hours > 0 ? "{$hours}h" . ($mins > 0 ? sprintf('%02d', $mins) : '') : "{$mins}m";
                @endphp
                <p class="text-2xl font-bold mt-3" style="color: {{ $balance > 0 ? '#687864' : ($balance < 0 ? '#31708E' : '#5085A5') }};">
                    {{ $balance > 0 ? '-' : ($balance < 0 ? '+' : '') }}{{ $balanceFormatted }}
                </p>
                <p class="text-xs text-gray-500">
                    @if($balance > 0)
                         rattraper
                    @elseif($balance < 0)
                        Surplus
                    @else
                        équilibré
                    @endif
                </p>
                @if($balance != 0)
                <p class="text-xs text-gray-400 mt-1">
                    Ce mois: {{ $recoveryStats['monthly_recovery'] ?? 0 }} min rattrapées
                </p>
                @endif
            </div>
        </div>

        <!-- Graphique + Calendrier -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Graphique hebdomadaire -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4"> Heures des 7 derniers jours</h3>
                <div class="h-72">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>

            <!-- Calendrier mensuel -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 Calendrier du mois</h3>
                <div class="max-w-md mx-auto">
                    <div class="grid grid-cols-7 gap-1 text-center text-xs mb-2">
                        <span class="text-gray-500 font-medium py-1">Lun</span>
                        <span class="text-gray-500 font-medium py-1">Mar</span>
                        <span class="text-gray-500 font-medium py-1">Mer</span>
                        <span class="text-gray-500 font-medium py-1">Jeu</span>
                        <span class="text-gray-500 font-medium py-1">Ven</span>
                        <span class="text-gray-500 font-medium py-1">Sam</span>
                        <span class="text-gray-500 font-medium py-1">Dim</span>
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
                                    'present' => 'border-emerald-400 hover:bg-emerald-100',
                                    'late' => 'border-orange-400 hover:bg-orange-100',
                                    'absent' => 'border-red-400 hover:bg-red-100',
                                    'leave' => 'border-purple-400 hover:bg-purple-100',
                                    'weekend' => 'bg-gray-50 text-gray-400 border-gray-200',
                                    'future' => 'bg-gray-50 text-gray-400 border-gray-200',
                                    'recovery' => 'border-cyan-400 hover:bg-cyan-100',
                                ];
                                $statusBg = [
                                    'present' => 'background: #d1fae5; color: #065f46;',
                                    'late' => 'background: #ffedd5; color: #9a3412;',
                                    'absent' => 'background: #fee2e2; color: #991b1b;',
                                    'leave' => 'background: #ede9fe; color: #5b21b6;',
                                    'recovery' => 'background: #cffafe; color: #155e75;',
                                ];
                                $class = $statusClasses[$day['status']] ?? 'bg-gray-50 text-gray-400 border-gray-200';
                                $bgStyle = $statusBg[$day['status']] ?? '';
                                $isToday = $day['date'] === now()->format('Y-m-d');
                                $title = $day['hours'] ? $day['hours'].'h travaillées' : '';
                                if ($day['status'] === 'recovery') {
                                    $title = 'Session de rattrapage' . ($day['hours'] ? ' - '.$day['hours'].'h' : '');
                                }
                            @endphp
                            <div class="aspect-square flex items-center justify-center text-xs font-semibold rounded-lg border-2 transition-all cursor-default {{ $class }} {{ $isToday ? 'ring-2 ring-blue-500 ring-offset-1' : '' }}" style="{{ $bgStyle }}" title="{{ $title }}">
                                {{ $day['day'] }}
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Légende -->
                    <div class="mt-4 flex flex-wrap justify-center gap-3 text-xs">
                        <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded" style="background: #d1fae5; border: 2px solid #34d399;"></span> <span class="text-gray-700 font-medium">Présent</span></div>
                        <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded" style="background: #ffedd5; border: 2px solid #fb923c;"></span> <span class="text-gray-700 font-medium">Retard</span></div>
                        <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded" style="background: #fee2e2; border: 2px solid #f87171;"></span> <span class="text-gray-700 font-medium">Absent</span></div>
                        <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded" style="background: #ede9fe; border: 2px solid #a78bfa;"></span> <span class="text-gray-700 font-medium">Congé</span></div>
                        <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded" style="background: #cffafe; border: 2px solid #22d3ee;"></span> <span class="text-gray-700 font-medium">Rattrapage</span></div>
                        <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 bg-gray-50 border-2 border-gray-300 rounded"></span> <span class="text-gray-700 font-medium">Non travaillé</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Rattrapage des Heures (Version compacte) -->
        @php
            $totalToRecover = $totalUnrecoveredMinutes;
            $totalHours = floor($totalToRecover / 60);
            $totalMins = $totalToRecover % 60;
            $hasLateToRecover = $totalToRecover > 0 || $expiredLate->isNotEmpty();
        @endphp
        
        @if($hasLateToRecover)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(to right, rgba(80, 133, 165, 0.1), rgba(143, 193, 227, 0.1));">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #5085A5, #8FC1E3); box-shadow: 0 10px 15px -3px rgba(80, 133, 165, 0.3);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900"> Heures à rattraper</h3>
                            <p class="text-xs text-gray-500">Heures sup ou session de rattrapage</p>
                        </div>
                    </div>
                    @if($totalToRecover > 0)
                    <div class="text-right">
                        <p class="text-2xl font-bold" style="color: #5085A5;">
                            {{ $totalHours > 0 ? $totalHours . 'h' : '' }}{{ $totalMins > 0 ? sprintf('%02d', $totalMins) : ($totalHours > 0 ? '00' : '0') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            @if($lateToRecoverCount > 5)
                                {{ $lateToRecoverCount }} retards
                            @else
                                à rattraper
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Retards à rattraper (compact) -->
                    <div>
                        <h4 class="font-medium text-gray-700 text-sm mb-2 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full" style="background: #5085A5;"></span>
                            En attente ({{ $lateToRecoverCount }})
                        </h4>
                        
                        @if($lateToRecover->isEmpty())
                            <p class="text-sm text-green-600 p-2"> Aucun retard récent</p>
                        @else
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($lateToRecover as $late)
                                    @php
                                        $isUrgent = ($late['days_remaining'] ?? 999) <= 2;
                                    @endphp
                                    <div class="p-2 rounded-lg border text-sm flex items-center justify-between" style="{{ $isUrgent ? 'border-color: rgba(104, 120, 100, 0.3); background: rgba(104, 120, 100, 0.08);' : 'border-color: #e5e7eb; background: #f9fafb;' }}">
                                        <div>
                                            <span class="font-medium">{{ $late['date']->format('d/m') }}</span>
                                            <span class="text-gray-400 mx-1">©·</span>
                                            <span class="text-gray-600">{{ $late['late_minutes'] }}min</span>
                                            @if($late['recovery_minutes'] > 0)
                                                <span class="text-green-600 text-xs">({{ $late['recovery_minutes'] }} rattrapé)</span>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <span class="font-semibold {{ $isUrgent ? 'text-red-600' : 'text-orange-600' }}">{{ $late['unrecovered_minutes'] }}min</span>
                                            @if($late['deadline'] && $isUrgent)
                                                <span class="text-xs text-red-500 block">{{ $late['deadline']->format('d/m') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($lateToRecoverCount > 5)
                                <p class="text-xs text-gray-400 mt-2 text-center">+ {{ $lateToRecoverCount - 5 }} autres retards</p>
                            @endif
                        @endif
                    </div>

                    <!-- Retards expirés (compact) -->
                    <div>
                        <h4 class="font-medium text-gray-700 text-sm mb-2 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                            Expirés
                            @php
                                $totalExpired = $expiringLateData['expired_minutes'];
                                $threshold = $expiringLateData['penalty_threshold'];
                            @endphp
                            @if($totalExpired > 0)
                                <span class="text-xs text-red-500">({{ $totalExpired }}min / {{ $threshold }}min)</span>
                            @endif
                        </h4>
                        
                        @if($expiredLate->isEmpty())
                            <p class="text-sm text-green-600 p-2"> Aucun expiré</p>
                        @else
                            <div class="space-y-1 max-h-32 overflow-y-auto">
                                @foreach($expiredLate as $expired)
                                    <div class="p-2 rounded-lg border border-red-100 bg-red-50/50 text-sm flex items-center justify-between">
                                        <span class="text-gray-700">{{ $expired->date->format('d/m/Y') }}</span>
                                        <span class="font-semibold text-red-600">{{ $expired->expired_late_minutes }}min</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Barre pénalité -->
                            @if($totalExpired > 0)
                                @php
                                    $penaltyProgress = $threshold > 0 ? min(100, ($totalExpired / $threshold) * 100) : 0;
                                @endphp
                                <div class="mt-2 p-2 rounded-lg" style="background: rgba(104, 120, 100, 0.1);">
                                    <div class="flex justify-between text-xs mb-1" style="color: #687864;">
                                        <span>Vers absence pénalité</span>
                                        <span class="font-bold">{{ round($penaltyProgress) }}%</span>
                                    </div>
                                    <div class="w-full rounded-full h-1.5" style="background: rgba(104, 120, 100, 0.2);">
                                        <div class="h-1.5 rounded-full" style="width: {{ $penaltyProgress }}%; background: #687864;"></div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Info compacte - 2 options de rattrapage -->
                <div class="mt-3 p-3 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100">
                    <div class="flex items-start gap-3">
                        <div class="bg-blue-100 p-1.5 rounded-lg">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-xs text-blue-800">
                            <p class="font-semibold mb-1"> Comment rattraper vos heures ?</p>
                            <ul class="space-y-1 text-blue-700">
                                <li><span class="font-medium">1.</span> Restez aprés {{ $workSettings['work_end'] ?? '17:00' }} vos jours de travail à rattrapage automatique</li>
                                <li><span class="font-medium">2.</span> Venez un jour non travaillé à démarrez une "session de rattrapage"</li>
                            </ul>
                            <p class="text-blue-600 mt-1 italic">Délai: {{ $expiringLateData['recovery_days'] }} jours par retard avant expiration.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Month Selector -->
        <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-200">
            <form action="{{ route('employee.presences.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="mois" class="block text-sm font-medium text-gray-700 mb-1">Afficher l'historique du mois</label>
                    <input type="month" name="mois" id="mois" value="{{ request('mois', now()->format('Y-m')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-6 py-2.5 text-white rounded-lg transition-all shadow-lg" style="background: linear-gradient(135deg, #31708E, #5085A5); box-shadow: 0 10px 15px -3px rgba(49, 112, 142, 0.25);">
                    Afficher
                </button>
            </form>
        </div>

        <!-- Historique Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900"> Historique des présences</h3>
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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rattrapage</th>
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
                                    @if($presence->check_in)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="{{ $presence->is_late ? 'background: rgba(104, 120, 100, 0.15); color: #687864;' : 'background: rgba(49, 112, 142, 0.15); color: #31708E;' }}">
                                        {{ $presence->check_in->format('H:i') }}
                                        @if($presence->is_late)
                                            <span class="ml-1">(+{{ $presence->late_minutes }}min)</span>
                                        @endif
                                    </span>
                                    @elseif($presence->pre_check_in)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium animate-pulse" style="background: rgba(99, 102, 241, 0.15); color: #6366f1;">
                                        🌅 {{ $presence->pre_check_in->format('H:i') }} <span class="ml-1">(pré-pointage)</span>
                                    </span>
                                    @else
                                    <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($presence->check_out)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="{{ $presence->is_early_departure ? 'background: rgba(80, 133, 165, 0.15); color: #5085A5;' : 'background: rgba(143, 193, 227, 0.2); color: #31708E;' }}">
                                            {{ $presence->check_out->format('H:i') }}
                                            @if($presence->is_early_departure)
                                                <span class="ml-1">(-{{ $presence->early_departure_minutes }}min)</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium animate-pulse" style="background: rgba(143, 193, 227, 0.2); color: #5085A5;">
                                            En cours...
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $presence->hours_worked ? number_format($presence->hours_worked, 1) . 'h' : '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($presence->is_recovery_session)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background: rgba(80, 133, 165, 0.2); color: #31708E;">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                            Rattrapage
                                        </span>
                                    @elseif($presence->is_late || $presence->is_early_departure)
                                        <div class="flex flex-wrap gap-1">
                                            @if($presence->is_late)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background: rgba(104, 120, 100, 0.15); color: #687864;">Retard</span>
                                            @endif
                                            @if($presence->is_early_departure)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background: rgba(80, 133, 165, 0.15); color: #5085A5;">{{ $presence->departure_type === 'urgence' ? '⚠️ Urgence' : 'Anticipé' }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background: rgba(49, 112, 142, 0.15); color: #31708E;"> OK</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($presence->overtime_minutes > 0 || $presence->recovery_minutes > 0)
                                        <div class="flex flex-col gap-1">
                                            @if($presence->overtime_minutes > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background: rgba(143, 193, 227, 0.2); color: #31708E;">
                                                    +{{ $presence->overtime_minutes }}min sup
                                                </span>
                                            @endif
                                            @if($presence->recovery_minutes > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background: rgba(80, 133, 165, 0.2); color: #5085A5;">
                                                     {{ $presence->recovery_minutes }}min rattrapées
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($presence->check_in_status === 'in_zone')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background: rgba(49, 112, 142, 0.15); color: #31708E;"> Zone</span>
                                    @elseif($presence->check_in_status === 'out_of_zone')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background: rgba(104, 120, 100, 0.15); color: #687864;">⚠️ Hors zone</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
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
    <script nonce="{{ $cspNonce ?? '' }}" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script nonce="{{ $cspNonce ?? '' }}">
        // Graphique hebdomadaire
        new Chart(document.getElementById('weeklyChart'), {
            type: 'bar',
            data: {
                labels: @json($weeklyLabels),
                datasets: [{
                    label: 'Heures travaillées',
                    data: @json($weeklyData),
                    backgroundColor: 'rgba(49, 112, 142, 0.8)',
                    borderColor: '#31708E',
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
    <script nonce="{{ $cspNonce ?? '' }}">
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
                    showError('⚠️ Navigateur non compatible', 'Votre navigateur ne supporte pas la géolocalisation.');
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
                                showError('Géolocalisation refusée', 'Veuillez autoriser l\'accés à votre position.');
                                break;
                            case error.POSITION_UNAVAILABLE:
                                showError(' Position indisponible', 'Impossible de déterminer votre position.');
                                break;
                            case error.TIMEOUT:
                                showError('⭐ Délai dépassé', 'La recherche de position a pris trop de temps.');
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

            // ===== Pré-pointage (arrivée anticipée) =====
            const preCheckInBtn = document.getElementById('preCheckInBtn');
            const preCheckInForm = document.getElementById('preCheckInForm');
            const preCheckInLat = document.getElementById('preCheckInLat');
            const preCheckInLng = document.getElementById('preCheckInLng');

            if (preCheckInBtn) {
                preCheckInBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!navigator.geolocation) {
                        showError('⚠️ Navigateur non compatible', 'Votre navigateur ne supporte pas la géolocalisation.');
                        return;
                    }

                    preCheckInBtn.disabled = true;
                    preCheckInBtn.querySelector('span').textContent = 'Recherche de position...';
                    hideError();

                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            preCheckInLat.value = position.coords.latitude;
                            preCheckInLng.value = position.coords.longitude;
                            preCheckInBtn.querySelector('span').textContent = 'Envoi en cours...';
                            preCheckInForm.submit();
                        },
                        function(error) {
                            preCheckInBtn.disabled = false;
                            preCheckInBtn.querySelector('span').textContent = '🌅 Je suis arrivé(e) — Pré-pointage';
                            showError('Erreur de géolocalisation', 'Veuillez autoriser l\'accès à votre position.');
                        },
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                    );
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
                        showError('⚠️ Navigateur non compatible', 'Votre navigateur ne supporte pas la géolocalisation.');
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
                            showError('Erreur de géolocalisation', 'Veuillez autoriser l\'accés à votre position.');
                        },
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                    );
                });
            }

            // ===== Session de rattrapage =====
            const recoveryStartBtn = document.getElementById('recoveryStartBtn');
            const recoveryStartForm = document.getElementById('recoveryStartForm');
            const recoveryStartLat = document.getElementById('recoveryStartLat');
            const recoveryStartLng = document.getElementById('recoveryStartLng');
            const recoveryStartText = document.getElementById('recoveryStartText');
            const recoveryStartIcon = document.getElementById('recoveryStartIcon');

            const recoveryEndBtn = document.getElementById('recoveryEndBtn');
            const recoveryEndForm = document.getElementById('recoveryEndForm');
            const recoveryEndLat = document.getElementById('recoveryEndLat');
            const recoveryEndLng = document.getElementById('recoveryEndLng');
            const recoveryEndText = document.getElementById('recoveryEndText');
            const recoveryEndIcon = document.getElementById('recoveryEndIcon');

            const recoveryStartOriginalIcon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>`;
            const recoveryEndOriginalIcon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>`;

            if (recoveryStartBtn) {
                recoveryStartBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    handlePointage(recoveryStartBtn, recoveryStartForm, recoveryStartLat, recoveryStartLng, recoveryStartText, recoveryStartIcon, 'Démarrer le rattrapage', recoveryStartOriginalIcon);
                });
            }

            if (recoveryEndBtn) {
                recoveryEndBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    handlePointage(recoveryEndBtn, recoveryEndForm, recoveryEndLat, recoveryEndLng, recoveryEndText, recoveryEndIcon, 'Terminer le rattrapage', recoveryEndOriginalIcon);
                });
            }
        });
    </script>
    @endif
</x-layouts.employee>
