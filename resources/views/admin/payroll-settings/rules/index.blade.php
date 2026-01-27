<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex items-center text-sm text-gray-500 mb-2">
                    <a href="{{ route('admin.payroll-settings.countries') }}" class="hover:text-red-600">Configuration Paie</a>
                    <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span>{{ $country->name }}</span>
                    <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-gray-900">Règles de Calcul</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">Règles de Calcul - {{ $country->name }}</h1>
            </div>
            <a href="{{ route('admin.payroll-settings.rules.create', $country) }}" 
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-sm font-medium rounded-lg hover:from-red-700 hover:to-red-800 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter une Règle
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Rules Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Calcul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taux/Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rules as $rule)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $rule->display_order }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-gray-100 text-gray-800">
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
                                            'tax' => 'bg-red-100 text-red-800',
                                            'contribution' => 'bg-blue-100 text-blue-800',
                                            'allowance' => 'bg-green-100 text-green-800',
                                            'deduction' => 'bg-yellow-100 text-yellow-800',
                                            'earning' => 'bg-purple-100 text-purple-800',
                                        ];
                                        $typeLabels = [
                                            'tax' => 'Taxe',
                                            'contribution' => 'Cotisation',
                                            'allowance' => 'Indemnité',
                                            'deduction' => 'Retenue',
                                            'earning' => 'Revenu',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$rule->rule_type] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $typeLabels[$rule->rule_type] ?? $rule->rule_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ucfirst($rule->calculation_type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @if($rule->calculation_type === 'percentage')
                                        {{ number_format($rule->rate, 2) }}%
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $rule->rule_category === 'employee' ? 'bg-blue-100 text-blue-800' : ($rule->rule_category === 'employer' ? 'bg-orange-100 text-orange-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ $rule->rule_category === 'employee' ? 'Employé' : ($rule->rule_category === 'employer' ? 'Employeur' : 'Les deux') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.payroll-settings.rules.edit', [$country, $rule]) }}" class="text-red-600 hover:text-red-900 mr-3">Modifier</a>
                                    <form action="{{ route('admin.payroll-settings.rules.destroy', [$country, $rule]) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette règle ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    Aucune règle de calcul définie pour ce pays.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-start">
            <a href="{{ route('admin.payroll-settings.countries') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour aux pays
            </a>
        </div>
    </div>
</x-layouts.admin>
