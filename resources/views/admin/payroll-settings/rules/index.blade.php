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
                                <li><a href="{{ route('admin.payroll-settings.countries') }}" class="text-white/70 hover:text-white text-sm">Configuration Paie</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white/70 text-sm">{{ $country->name }}</span></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Règles</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            Règles de Calcul - {{ $country->name }}
                        </h1>
                        <p class="text-white/80 mt-2">Gérez les règles fiscales et cotisations</p>
                    </div>
                    <a href="{{ route('admin.payroll-settings.rules.create', $country) }}" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter une Règle
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3 animate-fade-in-up">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Rules Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up animation-delay-100">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ordre</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Libellé</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Calcul</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Taux/Montant</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rules as $rule)
                            <tr class="hover:bg-purple-50/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $rule->display_order }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ $rule->code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $rule->label }}</div>
                                    @if($rule->description)
                                        <div class="text-xs text-gray-500">{{ Str::limit($rule->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeColors = [
                                            'tax' => 'background-color: rgba(239, 68, 68, 0.15); color: #ef4444;',
                                            'contribution' => 'background-color: rgba(86, 128, 233, 0.15); color: #5680E9;',
                                            'allowance' => 'background-color: rgba(90, 185, 234, 0.15); color: #5AB9EA;',
                                            'deduction' => 'background-color: rgba(136, 96, 208, 0.15); color: #8860D0;',
                                            'earning' => 'background-color: rgba(132, 206, 235, 0.15); color: #5680E9;',
                                        ];
                                        $typeLabels = [
                                            'tax' => 'Taxe',
                                            'contribution' => 'Cotisation',
                                            'allowance' => 'Indemnité',
                                            'deduction' => 'Retenue',
                                            'earning' => 'Revenu',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="{{ $typeColors[$rule->rule_type] ?? 'background-color: rgba(156, 163, 175, 0.15); color: #6b7280;' }}">
                                        {{ $typeLabels[$rule->rule_type] ?? $rule->rule_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ucfirst($rule->calculation_type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @if($rule->calculation_type === 'percentage')
                                        <span style="color: #5680E9;">{{ number_format($rule->rate, 2) }}%</span>
                                    @elseif($rule->calculation_type === 'fixed')
                                        {{ number_format($rule->fixed_amount, 0, ',', ' ') }} {{ $country->currency_symbol }}
                                    @elseif($rule->calculation_type === 'bracket')
                                        <span class="text-gray-500">Barème</span>
                                    @endif
                                    @if($rule->ceiling)
                                        <div class="text-xs text-gray-400">Plafond: {{ number_format($rule->ceiling, 0, ',', ' ') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($rule->rule_category === 'employee')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(86, 128, 233, 0.15); color: #5680E9;">Employé</span>
                                    @elseif($rule->rule_category === 'employer')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(136, 96, 208, 0.15); color: #8860D0;">Employeur</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(193, 200, 228, 0.3); color: #5680E9;">Les deux</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.payroll-settings.rules.edit', [$country, $rule]) }}" class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Modifier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.payroll-settings.rules.destroy', [$country, $rule]) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette règle ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-lg font-medium text-gray-900">Aucune règle de calcul</p>
                                        <p class="text-sm text-gray-500 mt-1">Aucune règle définie pour ce pays.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-start">
            <a href="{{ route('admin.payroll-settings.countries') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour aux pays
            </a>
        </div>
    </div>
</x-layouts.admin>
