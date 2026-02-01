<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="animate-fade-in-up">
            <nav class="flex items-center text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.payroll-settings.countries') }}" class="hover:text-red-600">Configuration Paie</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">Nouveau Pays</span>
            </nav>
            <h1  class=" bg-blue-600 text-2xl font-bold text-gray-900">Ajouter un Pays</h1>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.payroll-settings.countries.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-100">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code ISO (3 lettres) *</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required maxlength="3"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 uppercase"
                           placeholder="CIV, FRA, SEN...">
                    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du Pays *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Côte d'Ivoire">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Code Devise (3 lettres) *</label>
                    <input type="text" name="currency" id="currency" value="{{ old('currency') }}" required maxlength="3"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 uppercase"
                           placeholder="XOF, EUR...">
                </div>

                <div>
                    <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">Symbole Devise *</label>
                    <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="FCFA, €...">
                </div>

                <div class="md:col-span-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-gray-700">Actif</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.payroll-settings.countries') }}" 
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
</x-layouts.admin>
