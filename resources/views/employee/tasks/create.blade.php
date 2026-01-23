<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nouvelle tâche</h1>
                <p class="text-gray-500 mt-1">Proposer une nouvelle tâche à l'administrateur</p>
            </div>
            <a href="{{ route('employee.tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <form action="{{ route('employee.tasks.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div>
                    <label for="titre" class="block text-sm font-medium text-gray-700 mb-1">Titre de la tâche *</label>
                    <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('titre') border-red-500 @enderror">
                    @error('titre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="4" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="priorite" class="block text-sm font-medium text-gray-700 mb-1">Priorité *</label>
                        <select name="priorite" id="priorite" required class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('priorite') border-red-500 @enderror">
                            <option value="low" {{ old('priorite') === 'low' ? 'selected' : '' }}>Basse</option>
                            <option value="medium" {{ old('priorite', 'medium') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="high" {{ old('priorite') === 'high' ? 'selected' : '' }}>Haute</option>
                        </select>
                        @error('priorite')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('date_debut') border-red-500 @enderror">
                        @error('date_debut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin prévue</label>
                        <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin') }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('date_fin') border-red-500 @enderror">
                        @error('date_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-yellow-800">La tâche sera soumise pour approbation. Vous pourrez mettre à jour sa progression une fois approuvée.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('employee.tasks.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Annuler</a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        Soumettre la tâche
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.employee>
