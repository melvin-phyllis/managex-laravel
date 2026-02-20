<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header comme sur tasks -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl animate-fade-in-up" style="background: linear-gradient(135deg, #1B3C35, #3D7A6A) !important;">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <nav class="flex mb-3" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1">
                                <li><a href="{{ route('admin.dashboard') }}" class="text-white/70 hover:text-white text-sm">Dashboard</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><a href="{{ route('admin.employee-evaluations.index', ['month' => $month, 'year' => $year]) }}" class="text-white/70 hover:text-white text-sm">Évaluations</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Nouvelle</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            Nouvelle évaluation
                        </h1>
                        <p class="text-white/80 mt-2">{{ \Carbon\Carbon::create()->month((int) $month)->translatedFormat('F') }} {{ $year }}</p>
                    </div>
                    <a href="{{ route('admin.employee-evaluations.index', ['month' => $month, 'year' => $year]) }}" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-[#FBF7F0] transition-all shadow-lg flex items-center" style="color: #1B3C35;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.employee-evaluations.store') }}" method="POST" id="evaluationForm">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulaire principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Sélection employé -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Employé à évaluer</h3>
                        
                        @if($selectedEmployee)
                            <input type="hidden" name="user_id" value="{{ $selectedEmployee->id }}">
                            <div class="flex items-center gap-4 p-4 rounded-xl border" style="background-color: rgba(27, 60, 53, 0.05); border-color: rgba(27, 60, 53, 0.2);">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold text-lg" style="background: linear-gradient(135deg, #1B3C35, #3D7A6A);">
                                    {{ strtoupper(substr($selectedEmployee->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $selectedEmployee->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $selectedEmployee->poste ?? $selectedEmployee->contract_type }} | {{ $selectedEmployee->email }}</p>
                                </div>
                            </div>
                        @else
                            <select name="user_id" required class="w-full rounded-xl border-gray-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E]">
                                <option value="">-- Sélectionner un employé --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->name }} ({{ $employee->poste ?? $employee->contract_type }})
                                    </option>
                                @endforeach
                            </select>
                            @if($employees->isEmpty())
                                <p class="mt-2 text-sm text-amber-600">Tous les employés ont déjà été évalués ce mois.</p>
                            @endif
                        @endif
                        @error('user_id')
                            <p class="mt-2 text-sm text-[#1B3C35]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Critères d'évaluation -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Critères d'évaluation</h3>
                        
                        <div class="space-y-6">
                            @foreach($criteria as $key => $criterion)
                                <div class="p-4 bg-gray-50 rounded-xl">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="font-medium text-gray-900">{{ $criterion['label'] }}</label>
                                        <span class="text-sm px-2 py-0.5 rounded-full" style="background-color: rgba(27, 60, 53, 0.1); color: #1B3C35;">Max: {{ $criterion['max'] }} pts</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-3">{{ $criterion['description'] }}</p>
                                    <div class="flex items-center gap-4">
                                        <input type="range" 
                                               name="{{ $key }}" 
                                               id="{{ $key }}"
                                               min="0" 
                                               max="{{ $criterion['max'] }}" 
                                               step="0.5"
                                               value="{{ old($key, 0) }}"
                                               class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer criteria-input"
                                               style="accent-color: #1B3C35;"
                                               data-max="{{ $criterion['max'] }}">
                                        <div class="w-20 text-center">
                                            <input type="number" 
                                                   id="{{ $key }}_display"
                                                   min="0" 
                                                   max="{{ $criterion['max'] }}" 
                                                   step="0.5"
                                                   value="{{ old($key, 0) }}"
                                                   class="w-full text-center font-bold text-lg border-gray-300 rounded-lg focus:border-[#2D5A4E] focus:ring-[#2D5A4E] criteria-display"
                                                   data-target="{{ $key }}">
                                        </div>
                                    </div>
                                    @error($key)
                                        <p class="mt-2 text-sm text-[#1B3C35]">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Commentaires -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-300">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Commentaires</h3>
                        <textarea name="comments" rows="4" 
                                  class="w-full rounded-xl border-gray-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E]"
                                  placeholder="Observations, points d'amélioration, félicitations...">{{ old('comments') }}</textarea>
                    </div>
                </div>

                <!-- Résumé -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6 animate-fade-in-up animation-delay-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h3>
                        
                        <!-- Score total -->
                        <div class="text-center p-6 rounded-xl mb-6" style="background: linear-gradient(135deg, rgba(27, 60, 53, 0.1), rgba(132, 206, 235, 0.1));">
                            <p class="text-sm text-gray-500 mb-1">Note totale</p>
                            <div class="flex items-baseline justify-center gap-1">
                                <span id="totalScore" class="text-4xl font-bold" style="color: #1B3C35;">0.0</span>
                                <span class="text-xl text-gray-400">/5,5</span>
                            </div>
                            <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                                <div id="scoreBar" class="h-2 rounded-full transition-all duration-300" style="width: 0%; background: linear-gradient(90deg, #1B3C35, #3D7A6A);"></div>
                            </div>
                            <p id="scorePercentage" class="text-sm text-gray-500 mt-2">0%</p>
                        </div>

                        <!-- Salaire calculé -->
                        <div class="text-center p-6 rounded-xl mb-6" style="background: linear-gradient(135deg, rgba(45, 90, 78, 0.1), rgba(27, 60, 53, 0.1));">
                            <p class="text-sm text-gray-500 mb-1">Salaire brut calculé</p>
                            <p id="calculatedSalary" class="text-3xl font-bold" style="color: #1B3C35;">{{ number_format($smic, 0, ',', ' ') }} FCFA</p>
                            <p class="text-xs text-gray-500 mt-2">SMIC minimum garanti : {{ number_format($smic, 0, ',', ' ') }} FCFA</p>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-3">
                            <button type="submit" name="status" value="draft"
                                    class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium border border-gray-200">
                                Enregistrer en brouillon
                            </button>
                            <button type="submit" name="status" value="validated"
                                    class="w-full px-4 py-3 text-white rounded-xl transition-all font-semibold shadow-lg" style="background: linear-gradient(135deg, #1B3C35, #2D5A4E);">
                                Valider l'évaluation
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script nonce="{{ $cspNonce ?? '' }}">
        const smic = {{ $smic }};
        const maxScore = 5.5;

        function updateCalculations() {
            let total = 0;
            document.querySelectorAll('.criteria-input').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            total = Math.min(total, maxScore);

            // Update display
            document.getElementById('totalScore').textContent = total.toFixed(1);
            const percentage = (total / maxScore) * 100;
            document.getElementById('scoreBar').style.width = percentage + '%';
            document.getElementById('scorePercentage').textContent = percentage.toFixed(1) + '%';

            // Update score color
            const scoreEl = document.getElementById('totalScore');
            if (total >= 4) {
                scoreEl.style.color = '#1B3C35';
            } else if (total >= 2.5) {
                scoreEl.style.color = '#C8A96E';
            } else {
                scoreEl.style.color = '#ef4444';
            }

            // Calculate salary
            let salary = total * smic;
            salary = Math.max(smic, salary);
            document.getElementById('calculatedSalary').textContent = new Intl.NumberFormat('fr-FR').format(salary) + ' FCFA';
        }

        // Sync range and number inputs
        document.querySelectorAll('.criteria-input').forEach(range => {
            const display = document.getElementById(range.id + '_display');
            
            range.addEventListener('input', function() {
                display.value = this.value;
                updateCalculations();
            });
        });

        document.querySelectorAll('.criteria-display').forEach(display => {
            const target = display.dataset.target;
            const range = document.getElementById(target);
            
            display.addEventListener('input', function() {
                const max = parseFloat(range.dataset.max);
                let value = parseFloat(this.value) || 0;
                value = Math.min(Math.max(value, 0), max);
                this.value = value;
                range.value = value;
                updateCalculations();
            });
        });

        // Initial calculation
        updateCalculations();
    </script>
</x-layouts.admin>
