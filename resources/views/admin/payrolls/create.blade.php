<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between animate-fade-in-up">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nouvelle fiche de paie</h1>
                <p class="text-gray-500 mt-1">Créer une fiche de paie pour un employé</p>
            </div>
            <a href="{{ route('admin.payrolls.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fade-in-up animation-delay-100">
            <form action="{{ route('admin.payrolls.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Employé -->
                    <div class="md:col-span-2">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Employé *</label>
                        <select name="user_id" id="user_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('user_id') border-red-500 @enderror">
                            <option value="">Sélectionner un employé</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} - {{ $employee->poste ?? 'Poste non défini' }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mois -->
                    <div>
                        <label for="mois" class="block text-sm font-medium text-gray-700 mb-1">Mois *</label>
                        <select name="mois" id="mois" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('mois') border-red-500 @enderror">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('mois', now()->month) == $i ? 'selected' : '' }}>
                                    {{ ucfirst(\Carbon\Carbon::create()->month($i)->translatedFormat('F')) }}
                                </option>
                            @endfor
                        </select>
                        @error('mois')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Année -->
                    <div>
                        <label for="annee" class="block text-sm font-medium text-gray-700 mb-1">Année *</label>
                        <select name="annee" id="annee" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('annee') border-red-500 @enderror">
                            @for($i = now()->year; $i >= now()->year - 2; $i--)
                                <option value="{{ $i }}" {{ old('annee', now()->year) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('annee')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Montant -->
                    <div>
                        <label for="montant" class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA) *</label>
                        <input type="number" name="montant" id="montant" value="{{ old('montant') }}" step="0.01" min="0" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('montant') border-red-500 @enderror">
                        @error('montant')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                        <select name="statut" id="statut" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('statut') border-red-500 @enderror">
                            <option value="pending" {{ old('statut', 'pending') === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="paid" {{ old('statut') === 'paid' ? 'selected' : '' }}>Payé</option>
                        </select>
                        @error('statut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror" placeholder="Notes additionnelles...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.payrolls.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Annuler</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Créer la fiche de paie
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
