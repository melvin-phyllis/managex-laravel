<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Évaluation groupée</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ \Carbon\Carbon::create()->month((int) $month)->translatedFormat('F') }} {{ $year }} - 
                    {{ $employees->count() }} employé(s) à évaluer
                </p>
            </div>
            <a href="{{ route('admin.employee-evaluations.index', ['month' => $month, 'year' => $year]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>

        @if($employees->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-gray-500">Tous les employés ont déjà été évalués ce mois.</p>
            </div>
        @else
            <!-- Info -->
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Critères d'évaluation</p>
                        <p class="text-xs text-blue-700">
                            Résolution problèmes (max 2) | Objectifs (max 0,5) | Pression (max 1) | Rendre compte (max 2) | <strong>Total max: 5,5</strong>
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.employee-evaluations.bulk-store') }}" method="POST">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Employé</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Résolution pb<br><span class="text-gray-400">/2</span></th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Objectifs<br><span class="text-gray-400">/0,5</span></th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pression<br><span class="text-gray-400">/1</span></th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rendre compte<br><span class="text-gray-400">/2</span></th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total<br><span class="text-gray-400">/5,5</span></th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Salaire</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($employees as $index => $employee)
                                    <tr class="hover:bg-gray-50" data-row="{{ $index }}">
                                        <td class="px-4 py-3">
                                            <input type="hidden" name="evaluations[{{ $index }}][user_id]" value="{{ $employee->id }}">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900 text-sm">{{ $employee->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $employee->contract_type }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" 
                                                   name="evaluations[{{ $index }}][problem_solving]" 
                                                   min="0" max="2" step="0.5" 
                                                   value="0"
                                                   class="w-16 text-center border-gray-300 rounded focus:border-indigo-500 focus:ring-indigo-500 text-sm bulk-input"
                                                   data-row="{{ $index }}" data-max="2">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" 
                                                   name="evaluations[{{ $index }}][objectives_respect]" 
                                                   min="0" max="0.5" step="0.5" 
                                                   value="0"
                                                   class="w-16 text-center border-gray-300 rounded focus:border-indigo-500 focus:ring-indigo-500 text-sm bulk-input"
                                                   data-row="{{ $index }}" data-max="0.5">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" 
                                                   name="evaluations[{{ $index }}][work_under_pressure]" 
                                                   min="0" max="1" step="0.5" 
                                                   value="0"
                                                   class="w-16 text-center border-gray-300 rounded focus:border-indigo-500 focus:ring-indigo-500 text-sm bulk-input"
                                                   data-row="{{ $index }}" data-max="1">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" 
                                                   name="evaluations[{{ $index }}][accountability]" 
                                                   min="0" max="2" step="0.5" 
                                                   value="0"
                                                   class="w-16 text-center border-gray-300 rounded focus:border-indigo-500 focus:ring-indigo-500 text-sm bulk-input"
                                                   data-row="{{ $index }}" data-max="2">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="row-total font-bold text-indigo-600" data-row="{{ $index }}">0</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="row-salary font-bold text-green-600" data-row="{{ $index }}">{{ number_format($smic, 0, ',', ' ') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                        <div class="text-sm text-gray-500">
                            <span id="totalEmployees">{{ $employees->count() }}</span> employé(s) | 
                            Salaire total estimé : <span id="grandTotal" class="font-bold text-gray-900">{{ number_format($employees->count() * $smic, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <button type="submit" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                            Enregistrer toutes les évaluations
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>

    <script>
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
            totalEl.classList.remove('text-green-600', 'text-yellow-600', 'text-red-600', 'text-indigo-600');
            if (total >= 4) {
                totalEl.classList.add('text-green-600');
            } else if (total >= 2.5) {
                totalEl.classList.add('text-yellow-600');
            } else if (total > 0) {
                totalEl.classList.add('text-red-600');
            } else {
                totalEl.classList.add('text-indigo-600');
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
