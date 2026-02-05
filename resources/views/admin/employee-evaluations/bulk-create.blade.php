<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header comme sur tasks -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl animate-fade-in-up" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;">
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
                                <li><span class="text-white text-sm font-medium">Groupée</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            Évaluation groupée
                        </h1>
                        <p class="text-white/80 mt-2">{{ \Carbon\Carbon::create()->month((int) $month)->translatedFormat('F') }} {{ $year }} · {{ $employees->count() }} employé(s) à évaluer</p>
                    </div>
                    <a href="{{ route('admin.employee-evaluations.index', ['month' => $month, 'year' => $year]) }}" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        @if($employees->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background: linear-gradient(135deg, #84CEEB, #5680E9);">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-lg font-medium text-gray-900">Tout est à jour !</p>
                <p class="text-gray-500 mt-1">Tous les employés ont déjà été évalués ce mois.</p>
            </div>
        @else
            <!-- Info des critères -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-center gap-3 animate-fade-in-up">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-indigo-900">Critères d'évaluation</p>
                    <p class="text-xs text-indigo-700">
                        Résolution problèmes <span class="font-semibold">(max 2)</span> | 
                        Objectifs <span class="font-semibold">(max 0,5)</span> | 
                        Pression <span class="font-semibold">(max 1)</span> | 
                        Rendre compte <span class="font-semibold">(max 2)</span> | 
                        <strong>Total max: 5,5</strong>
                    </p>
                </div>
            </div>

            <form action="{{ route('admin.employee-evaluations.bulk-store') }}" method="POST">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-56">Employé</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Résolution pb<br><span class="text-gray-400 font-normal">/2</span></th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Objectifs<br><span class="text-gray-400 font-normal">/0,5</span></th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Pression<br><span class="text-gray-400 font-normal">/1</span></th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Rendre compte<br><span class="text-gray-400 font-normal">/2</span></th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Total<br><span class="text-gray-400 font-normal">/5,5</span></th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Salaire</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($employees as $index => $employee)
                                    <tr class="hover:bg-purple-50/50 transition-colors" data-row="{{ $index }}">
                                        <td class="px-6 py-4">
                                            <input type="hidden" name="evaluations[{{ $index }}][user_id]" value="{{ $employee->id }}">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full flex items-center justify-center ring-2 ring-white shadow-sm" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                                    <span class="text-white font-bold text-xs">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900 text-sm">{{ $employee->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $employee->contract_type }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <input type="number" 
                                                   name="evaluations[{{ $index }}][problem_solving]" 
                                                   min="0" max="2" step="0.5" 
                                                   value="0"
                                                   class="w-16 text-center border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium bulk-input"
                                                   data-row="{{ $index }}" data-max="2">
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <input type="number" 
                                                   name="evaluations[{{ $index }}][objectives_respect]" 
                                                   min="0" max="0.5" step="0.5" 
                                                   value="0"
                                                   class="w-16 text-center border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium bulk-input"
                                                   data-row="{{ $index }}" data-max="0.5">
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <input type="number" 
                                                   name="evaluations[{{ $index }}][work_under_pressure]" 
                                                   min="0" max="1" step="0.5" 
                                                   value="0"
                                                   class="w-16 text-center border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium bulk-input"
                                                   data-row="{{ $index }}" data-max="1">
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <input type="number" 
                                                   name="evaluations[{{ $index }}][accountability]" 
                                                   min="0" max="2" step="0.5" 
                                                   value="0"
                                                   class="w-16 text-center border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium bulk-input"
                                                   data-row="{{ $index }}" data-max="2">
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="row-total font-bold" style="color: #8860D0;" data-row="{{ $index }}">0</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="row-salary font-bold" style="color: #5680E9;" data-row="{{ $index }}">{{ number_format($smic, 0, ',', ' ') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                        <div class="text-sm text-gray-600">
                            <span class="font-semibold" id="totalEmployees">{{ $employees->count() }}</span> employé(s) | 
                            Salaire total estimé : <span id="grandTotal" class="font-bold" style="color: #5680E9;">{{ number_format($employees->count() * $smic, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <button type="submit" class="px-6 py-3 text-white font-semibold rounded-xl transition-all shadow-lg flex items-center" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Enregistrer toutes les évaluations
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>

    <script nonce="{{ $cspNonce ?? '' }}">
        const smic = {{ $smic }};

        function updateRow(rowIndex) {
            const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
            if (!row) return;

            const inputs = row.querySelectorAll('.bulk-input');
            let total = 0;
            inputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            total = Math.min(total, 5.5);

            const totalEl = row.querySelector('.row-total');
            const salaryEl = row.querySelector('.row-salary');

            totalEl.textContent = total.toFixed(1);
            
            let salary = total * smic;
            salary = Math.max(smic, salary);
            salaryEl.textContent = new Intl.NumberFormat('fr-FR').format(salary);

            // Color based on score
            if (total >= 4) {
                totalEl.style.color = '#5680E9';
            } else if (total >= 2.5) {
                totalEl.style.color = '#8860D0';
            } else if (total > 0) {
                totalEl.style.color = '#ef4444';
            } else {
                totalEl.style.color = '#8860D0';
            }

            updateGrandTotal();
        }

        function updateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.row-salary').forEach(el => {
                grandTotal += parseInt(el.textContent.replace(/\s/g, '')) || 0;
            });
            document.getElementById('grandTotal').textContent = new Intl.NumberFormat('fr-FR').format(grandTotal) + ' FCFA';
        }

        document.querySelectorAll('.bulk-input').forEach(input => {
            input.addEventListener('input', function() {
                const max = parseFloat(this.dataset.max);
                let value = parseFloat(this.value) || 0;
                if (value > max) {
                    this.value = max;
                    value = max;
                }
                if (value < 0) {
                    this.value = 0;
                }
                updateRow(this.dataset.row);
            });
        });
    </script>
</x-layouts.admin>
