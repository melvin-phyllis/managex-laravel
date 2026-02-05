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
                                <li><span class="text-white text-sm font-medium">Modifier {{ $country->name }}</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            Modifier - {{ $country->name }}
                        </h1>
                        <p class="text-white/80 mt-2">Modifiez les paramètres de ce pays</p>
                    </div>
                    <a href="{{ route('admin.payroll-settings.countries') }}" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.payroll-settings.countries.update', $country) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-100">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code ISO</label>
                    <input type="text" value="{{ $country->code }}" disabled
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                    <p class="mt-1 text-xs text-gray-500">Le code ne peut pas être modifié</p>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du Pays *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $country->name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Code Devise *</label>
                    <input type="text" name="currency" id="currency" value="{{ old('currency', $country->currency) }}" required maxlength="3"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 uppercase">
                </div>

                <div>
                    <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">Symbole Devise *</label>
                    <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $country->currency_symbol) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $country->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 focus:ring-indigo-500" style="color: #5680E9;">
                        <span class="ml-2 text-sm text-gray-700">Actif</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-between mt-8 pt-6 border-t border-gray-100">
                <form action="{{ route('admin.payroll-settings.countries.destroy', $country) }}" method="POST" onsubmit="return confirm('Supprimer ce pays et toutes ses règles ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2.5 text-sm font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-xl transition-colors">
                        Supprimer ce pays
                    </button>
                </form>
                <div class="flex gap-3">
                    <a href="{{ route('admin.payroll-settings.countries') }}" 
                       class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl shadow-lg transition-all" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                        Enregistrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
