<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header avec gradient -->
        <div class="bg-gradient-to-r from-teal-600 via-cyan-600 to-blue-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Nouvelle demande de congé</h1>
                    <p class="text-teal-100">Soumettre une demande pour approbation</p>
                </div>
                <a href="{{ route('employee.leaves.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Informations de la demande
                </h3>
            </div>
            
            <form action="{{ route('employee.leaves.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Type de congé -->
                <div x-data="{ selectedType: '{{ old('type', '') }}' }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de congé *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="relative cursor-pointer" @click="selectedType = 'conge'">
                            <input type="radio" name="type" value="conge" x-model="selectedType" class="sr-only" required>
                            <div class="p-4 border-2 rounded-xl transition-all"
                                 :class="selectedType === 'conge' ? 'border-teal-500 bg-teal-50 ring-2 ring-teal-500 ring-offset-2' : 'border-gray-200 hover:bg-gray-50'">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Congé payé</p>
                                        <p class="text-xs text-gray-500">Vacances, repos</p>
                                    </div>
                                    <div x-show="selectedType === 'conge'" class="ml-auto">
                                        <svg class="w-6 h-6 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="relative cursor-pointer" @click="selectedType = 'maladie'">
                            <input type="radio" name="type" value="maladie" x-model="selectedType" class="sr-only">
                            <div class="p-4 border-2 rounded-xl transition-all"
                                 :class="selectedType === 'maladie' ? 'border-red-500 bg-red-50 ring-2 ring-red-500 ring-offset-2' : 'border-gray-200 hover:bg-gray-50'">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-rose-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Arrêt maladie</p>
                                        <p class="text-xs text-gray-500">Raison médicale</p>
                                    </div>
                                    <div x-show="selectedType === 'maladie'" class="ml-auto">
                                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="relative cursor-pointer" @click="selectedType = 'autre'">
                            <input type="radio" name="type" value="autre" x-model="selectedType" class="sr-only">
                            <div class="p-4 border-2 rounded-xl transition-all"
                                 :class="selectedType === 'autre' ? 'border-amber-500 bg-amber-50 ring-2 ring-amber-500 ring-offset-2' : 'border-gray-200 hover:bg-gray-50'">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Autre</p>
                                        <p class="text-xs text-gray-500">Événement familial...</p>
                                    </div>
                                    <div x-show="selectedType === 'autre'" class="ml-auto">
                                        <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-2">Date de début *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="date" name="date_debut" id="date_debut" 
                                   value="{{ old('date_debut') }}" 
                                   required 
                                   min="{{ now()->format('Y-m-d') }}" 
                                   class="w-full pl-10 rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 @error('date_debut') border-red-500 @enderror">
                        </div>
                        @error('date_debut')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-2">Date de fin *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="date" name="date_fin" id="date_fin" 
                                   value="{{ old('date_fin') }}" 
                                   required 
                                   min="{{ now()->format('Y-m-d') }}" 
                                   class="w-full pl-10 rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 @error('date_fin') border-red-500 @enderror">
                        </div>
                        @error('date_fin')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Durée calculée -->
                <div id="duration-info" class="hidden p-4 bg-teal-50 border border-teal-200 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-teal-800">Durée de la demande</p>
                            <p class="text-lg font-bold text-teal-700" id="duration-text">-</p>
                        </div>
                    </div>
                </div>

                <!-- Motif -->
                <div>
                    <label for="motif" class="block text-sm font-medium text-gray-700 mb-2">Motif de la demande *</label>
                    <textarea name="motif" id="motif" rows="4" required 
                              class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 @error('motif') border-red-500 @enderror" 
                              placeholder="Expliquez la raison de votre demande...">{{ old('motif') }}</textarea>
                    @error('motif')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info -->
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Information</p>
                            <p class="text-sm text-blue-700 mt-1">Votre demande sera envoyée à l'administrateur pour approbation. Vous recevrez une notification dès qu'une décision sera prise.</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('employee.leaves.index') }}" 
                       class="w-full sm:w-auto px-6 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors text-center">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition-colors shadow-md inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Soumettre la demande
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const dateDebut = document.getElementById('date_debut');
        const dateFin = document.getElementById('date_fin');
        const durationInfo = document.getElementById('duration-info');
        const durationText = document.getElementById('duration-text');

        function calculateDuration() {
            if (dateDebut.value && dateFin.value) {
                const start = new Date(dateDebut.value);
                const end = new Date(dateFin.value);
                
                if (end >= start) {
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    
                    durationInfo.classList.remove('hidden');
                    durationText.textContent = diffDays + ' jour' + (diffDays > 1 ? 's' : '');
                } else {
                    durationInfo.classList.add('hidden');
                }
            } else {
                durationInfo.classList.add('hidden');
            }
        }

        dateDebut.addEventListener('change', function() {
            dateFin.min = this.value;
            if (dateFin.value && dateFin.value < this.value) {
                dateFin.value = this.value;
            }
            calculateDuration();
        });

        dateFin.addEventListener('change', calculateDuration);
        
        // Initial calculation
        calculateDuration();
    </script>
</x-layouts.employee>
