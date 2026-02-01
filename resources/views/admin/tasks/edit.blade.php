<x-layouts.admin>
    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex animate-fade-in-up" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <a href="{{ route('admin.tasks.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Tâches</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Modifier</span>
                    </div>
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="flex items-center justify-between animate-fade-in-up animation-delay-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Modifier la tâche</h1>
                <p class="text-gray-500 mt-1">{{ $task->titre }}</p>
            </div>
            <a href="{{ route('admin.tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fade-in-up animation-delay-200">
            <form action="{{ route('admin.tasks.update', $task) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Employé -->
                    <div class="md:col-span-2">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Assigner à *</label>
                        <select name="user_id" id="user_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('user_id') border-red-500 @enderror">
                            <option value="">Sélectionner un employé</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('user_id', $task->user_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} - {{ $employee->poste ?? 'Poste non défini' }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Titre -->
                    <div class="md:col-span-2">
                        <label for="titre" class="block text-sm font-medium text-gray-700 mb-1">Titre de la tâche *</label>
                        <input type="text" name="titre" id="titre" value="{{ old('titre', $task->titre) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('titre') border-red-500 @enderror">
                        @error('titre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priorité -->
                    <div>
                        <label for="priorite" class="block text-sm font-medium text-gray-700 mb-1">Priorité *</label>
                        <select name="priorite" id="priorite" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('priorite') border-red-500 @enderror">
                            <option value="low" {{ old('priorite', $task->priorite) === 'low' ? 'selected' : '' }}>Basse</option>
                            <option value="medium" {{ old('priorite', $task->priorite) === 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="high" {{ old('priorite', $task->priorite) === 'high' ? 'selected' : '' }}>Haute</option>
                        </select>
                        @error('priorite')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                        <select name="statut" id="statut" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('statut') border-red-500 @enderror">
                            <option value="pending" {{ old('statut', $task->statut) === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="approved" {{ old('statut', $task->statut) === 'approved' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ old('statut', $task->statut) === 'completed' ? 'selected' : '' }}>Terminée (à valider)</option>
                            <option value="validated" {{ old('statut', $task->statut) === 'validated' ? 'selected' : '' }}>Validée</option>
                            <option value="rejected" {{ old('statut', $task->statut) === 'rejected' ? 'selected' : '' }}>Rejetée</option>
                        </select>
                        @error('statut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date début -->
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut', $task->date_debut?->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('date_debut') border-red-500 @enderror">
                        @error('date_debut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date fin -->
                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin (échéance)</label>
                        <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin', $task->date_fin?->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('date_fin') border-red-500 @enderror">
                        @error('date_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Progression (lecture seule) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Progression actuelle</label>
                        <div class="flex items-center space-x-3">
                            <div class="flex-1 bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $task->progression }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ $task->progression }}%</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">La progression est mise à jour par l'employé.</p>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.tasks.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Annuler</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
