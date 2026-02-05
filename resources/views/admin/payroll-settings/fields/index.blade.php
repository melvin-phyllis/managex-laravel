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
                                <li><span class="text-white text-sm font-medium">Champs</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                </svg>
                            </div>
                            Champs Dynamiques - {{ $country->name }}
                        </h1>
                        <p class="text-white/80 mt-2">Gérez les champs personnalisés pour les bulletins de paie</p>
                    </div>
                    <a href="{{ route('admin.payroll-settings.countries') }}" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour aux Pays
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

        <!-- Existing Fields -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up animation-delay-100">
            <div class="p-4 border-b border-gray-100 bg-gray-50">
                <h2 class="font-semibold text-gray-900">Champs Configurés</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Champ</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Libellé</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Section</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Imposable</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($fields as $field)
                            <tr class="hover:bg-purple-50/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm bg-gray-100 px-2.5 py-1 rounded-lg">{{ $field->field_name }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $field->field_label }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(86, 128, 233, 0.15); color: #5680E9;">
                                        {{ $field->field_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $field->section }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($field->is_taxable)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(90, 185, 234, 0.15); color: #5680E9;">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Oui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Non</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                                        <form action="{{ route('admin.payroll-settings.fields.destroy', [$country, $field]) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce champ ?')">
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
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                            </svg>
                                        </div>
                                        <p class="text-lg font-medium text-gray-900">Aucun champ dynamique</p>
                                        <p class="text-sm text-gray-500 mt-1">Ajoutez un champ ci-dessous pour commencer.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add New Field Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-200">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(86, 128, 233, 0.15);">
                    <svg class="w-4 h-4" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                Ajouter un Champ
            </h2>
            <form action="{{ route('admin.payroll-settings.fields.store', $country) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="field_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du Champ *</label>
                        <input type="text" name="field_name" id="field_name" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="transport_allowance">
                    </div>
                    <div>
                        <label for="field_label" class="block text-sm font-medium text-gray-700 mb-1">Libellé *</label>
                        <input type="text" name="field_label" id="field_label" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Indemnité de Transport">
                    </div>
                    <div>
                        <label for="field_type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                        <select name="field_type" id="field_type" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="number">Nombre</option>
                            <option value="text">Texte</option>
                            <option value="select">Liste</option>
                            <option value="boolean">Oui/Non</option>
                            <option value="date">Date</option>
                        </select>
                    </div>
                    <div>
                        <label for="section" class="block text-sm font-medium text-gray-700 mb-1">Section *</label>
                        <select name="section" id="section" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="allowances">Indemnités</option>
                            <option value="deductions">Retenues</option>
                            <option value="earnings">Revenus</option>
                            <option value="info">Informations</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_taxable" value="1" checked
                                   class="rounded border-gray-300 focus:ring-indigo-500" style="color: #5680E9;">
                            <span class="ml-2 text-sm text-gray-700">Imposable</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_required" value="1"
                                   class="rounded border-gray-300 focus:ring-indigo-500" style="color: #5680E9;">
                            <span class="ml-2 text-sm text-gray-700">Obligatoire</span>
                        </label>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full px-4 py-2.5 text-sm font-semibold text-white rounded-xl shadow-lg transition-all" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                            Ajouter le Champ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
