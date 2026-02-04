<x-layouts.admin>
    <div class="space-y-6">
        <div class="animate-fade-in-up">
            <nav class="flex items-center text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.payroll-settings.countries') }}" class="hover:text-red-600">Configuration Paie</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('admin.payroll-settings.rules', $country) }}" class="hover:text-red-600">{{ $country->name }}</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">Modifier {{ $rule->code }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Modifier la Régle - {{ $rule->label }}</h1>
        </div>

        <form action="{{ route('admin.payroll-settings.rules.update', [$country, $rule]) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-100">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" value="{{ $rule->code }}" disabled
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                </div>

                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700 mb-1">Libellé *</label>
                    <input type="text" name="label" id="label" value="{{ old('label', $rule->label) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <div>
                    <label for="rule_type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="rule_type" id="rule_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="tax" {{ old('rule_type', $rule->rule_type) === 'tax' ? 'selected' : '' }}>Taxe</option>
                        <option value="contribution" {{ old('rule_type', $rule->rule_type) === 'contribution' ? 'selected' : '' }}>Cotisation</option>
                        <option value="allowance" {{ old('rule_type', $rule->rule_type) === 'allowance' ? 'selected' : '' }}>Indemnité</option>
                        <option value="deduction" {{ old('rule_type', $rule->rule_type) === 'deduction' ? 'selected' : '' }}>Retenue</option>
                        <option value="earning" {{ old('rule_type', $rule->rule_type) === 'earning' ? 'selected' : '' }}>Revenu</option>
                    </select>
                </div>

                <div>
                    <label for="rule_category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                    <select name="rule_category" id="rule_category" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="employee" {{ old('rule_category', $rule->rule_category) === 'employee' ? 'selected' : '' }}>Employé</option>
                        <option value="employer" {{ old('rule_category', $rule->rule_category) === 'employer' ? 'selected' : '' }}>Employeur</option>
                        <option value="both" {{ old('rule_category', $rule->rule_category) === 'both' ? 'selected' : '' }}>Les deux</option>
                    </select>
                </div>

                <div>
                    <label for="calculation_type" class="block text-sm font-medium text-gray-700 mb-1">Mode de Calcul *</label>
                    <select name="calculation_type" id="calculation_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            onchange="toggleCalculationFields()">
                        <option value="percentage" {{ old('calculation_type', $rule->calculation_type) === 'percentage' ? 'selected' : '' }}>Pourcentage</option>
                        <option value="fixed" {{ old('calculation_type', $rule->calculation_type) === 'fixed' ? 'selected' : '' }}>Montant Fixe</option>
                        <option value="bracket" {{ old('calculation_type', $rule->calculation_type) === 'bracket' ? 'selected' : '' }}>Baréme</option>
                    </select>
                </div>

                <div>
                    <label for="base_field" class="block text-sm font-medium text-gray-700 mb-1">Base de Calcul *</label>
                    <select name="base_field" id="base_field" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="taxable_gross" {{ old('base_field', $rule->base_field) === 'taxable_gross' ? 'selected' : '' }}>Brut Imposable</option>
                        <option value="gross_salary" {{ old('base_field', $rule->base_field) === 'gross_salary' ? 'selected' : '' }}>Salaire Brut</option>
                        <option value="igr_base" {{ old('base_field', $rule->base_field) === 'igr_base' ? 'selected' : '' }}>Base IGR</option>
                    </select>
                </div>

                <div id="rate_field">
                    <label for="rate" class="block text-sm font-medium text-gray-700 mb-1">Taux (%)</label>
                    <input type="number" name="rate" id="rate" value="{{ old('rate', $rule->rate) }}" step="0.01" min="0" max="100"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <div id="fixed_field" style="display: none;">
                    <label for="fixed_amount" class="block text-sm font-medium text-gray-700 mb-1">Montant Fixe</label>
                    <input type="number" name="fixed_amount" id="fixed_amount" value="{{ old('fixed_amount', $rule->fixed_amount) }}" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <div>
                    <label for="ceiling" class="block text-sm font-medium text-gray-700 mb-1">Plafond</label>
                    <input type="number" name="ceiling" id="ceiling" value="{{ old('ceiling', $rule->ceiling) }}" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 mb-1">Ordre d'Affichage</label>
                    <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $rule->display_order) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <div>
                    <label for="pdf_code" class="block text-sm font-medium text-gray-700 mb-1">Code PDF</label>
                    <input type="text" name="pdf_code" id="pdf_code" value="{{ old('pdf_code', $rule->pdf_code) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <div id="brackets_field" class="md:col-span-2" style="display: none;">
                    <label for="brackets" class="block text-sm font-medium text-gray-700 mb-1">Baréme (JSON)</label>
                    <textarea name="brackets" id="brackets" rows="5"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 font-mono text-sm">{{ old('brackets', json_encode($rule->brackets, JSON_PRETTY_PRINT)) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ old('description', $rule->description) }}</textarea>
                </div>

                <div class="md:col-span-2 flex flex-wrap gap-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_deductible" value="1" {{ old('is_deductible', $rule->is_deductible) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-gray-700">Déductible de la base IGR</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_mandatory" value="1" {{ old('is_mandatory', $rule->is_mandatory) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-gray-700">Obligatoire</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_visible_on_payslip" value="1" {{ old('is_visible_on_payslip', $rule->is_visible_on_payslip) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-gray-700">Visible sur le bulletin</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.payroll-settings.rules', $country) }}" 
                   class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 rounded-lg hover:from-red-700 hover:to-red-800 bg-green-500">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

    <script nonce="{{ $cspNonce ?? '' }}">
        function toggleCalculationFields() {
            const type = document.getElementById('calculation_type').value;
            document.getElementById('rate_field').style.display = (type === 'percentage') ? 'block' : 'none';
            document.getElementById('fixed_field').style.display = (type === 'fixed') ? 'block' : 'none';
            document.getElementById('brackets_field').style.display = (type === 'bracket') ? 'block' : 'none';
        }
        toggleCalculationFields();
    </script>
</x-layouts.admin>
