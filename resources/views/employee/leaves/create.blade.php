<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nouvelle demande de congé</h1>
                <p class="text-gray-500 mt-1">Soumettre une demande pour approbation</p>
            </div>
            <a href="{{ route('employee.leaves.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <form action="{{ route('employee.leaves.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type de congé *</label>
                        <select name="type" id="type" required class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('type') border-red-500 @enderror">
                            <option value="">Sélectionner un type</option>
                            <option value="conge" {{ old('type') === 'conge' ? 'selected' : '' }}>Congé payé</option>
                            <option value="maladie" {{ old('type') === 'maladie' ? 'selected' : '' }}>Arrêt maladie</option>
                            <option value="autre" {{ old('type') === 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début *</label>
                        <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}" required min="{{ now()->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('date_debut') border-red-500 @enderror">
                        @error('date_debut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin *</label>
                        <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin') }}" required min="{{ now()->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('date_fin') border-red-500 @enderror">
                        @error('date_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="motif" class="block text-sm font-medium text-gray-700 mb-1">Motif *</label>
                    <textarea name="motif" id="motif" rows="4" required class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('motif') border-red-500 @enderror" placeholder="Expliquez la raison de votre demande...">{{ old('motif') }}</textarea>
                    @error('motif')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-blue-800">Votre demande sera envoyée à l'administrateur pour approbation. Vous recevrez une notification dès qu'une décision sera prise.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('employee.leaves.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Annuler</a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        Soumettre la demande
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('date_debut').addEventListener('change', function() {
            document.getElementById('date_fin').min = this.value;
        });
    </script>
</x-layouts.employee>
