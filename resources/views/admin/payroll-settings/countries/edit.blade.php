<x-layouts.admin>
    <div class="space-y-6">
        <div>
            <nav class="flex items-center text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.payroll-settings.countries') }}" class="hover:text-red-600">Configuration Paie</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">Modifier {{ $country->name }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Modifier - {{ $country->name }}</h1>
        </div>

        <form action="{{ route('admin.payroll-settings.countries.update', $country) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code ISO</label>
                    <input type="text" value="{{ $country->code }}" disabled
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                    <p class="mt-1 text-xs text-gray-500">Le code ne peut pas être modifié</p>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du Pays *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $country->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Code Devise *</label>
                    <input type="text" name="currency" id="currency" value="{{ old('currency', $country->currency) }}" required maxlength="3"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 uppercase">
                </div>

                <div>
                    <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">Symbole Devise *</label>
                    <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $country->currency_symbol) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <div class="md:col-span-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $country->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-gray-700">Actif</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                <form action="{{ route('admin.payroll-settings.countries.destroy', $country) }}" method="POST" onsubmit="return confirm('Supprimer ce pays et toutes ses règles ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800">
                        Supprimer ce pays
                    </button>
                </form>
                <div class="flex gap-4">
                    <a href="{{ route('admin.payroll-settings.countries') }}" 
                       class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 rounded-lg hover:from-red-700 hover:to-red-800 bg-green-500">
                        Enregistrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
