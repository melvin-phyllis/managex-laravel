<x-layouts.admin>
    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex animate-fade-in-up" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700" style="--hover-color: #5680E9;" onmouseover="this.style.color='#5680E9'" onmouseout="this.style.color=''">
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
                        <a href="{{ route('admin.tasks.index') }}" class="ml-1 text-sm font-medium text-gray-700 md:ml-2" style="--hover-color: #5680E9;" onmouseover="this.style.color='#5680E9'" onmouseout="this.style.color=''">Tâches</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Assigner</span>
                    </div>
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="flex items-center justify-between animate-fade-in-up animation-delay-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Assigner une tâche</h1>
                <p class="text-gray-500 mt-1">Créer et assigner une nouvelle tâche à un employé</p>
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
            <form action="{{ route('admin.tasks.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Employé -->
                    <div class="md:col-span-2">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Assigner à *</label>
                        <select name="user_id" id="user_id" required class="w-full rounded-lg border-gray-300 @error('user_id') border-red-500 @enderror" style="--tw-ring-opacity: 1; --tw-ring-color: #5680E9;" onfocus="this.style.borderColor='#5680E9'" onblur="this.style.borderColor=''">
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

                    <!-- Titre -->
                    <div class="md:col-span-2">
                        <label for="titre" class="block text-sm font-medium text-gray-700 mb-1">Titre de la tâche *</label>
                        <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required class="w-full rounded-lg border-gray-300 @error('titre') border-red-500 @enderror" placeholder="Ex: Préparer le rapport mensuel" style="--tw-ring-opacity: 1; --tw-ring-color: #5680E9;" onfocus="this.style.borderColor='#5680E9'" onblur="this.style.borderColor=''">
                        @error('titre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="4" class="w-full rounded-lg border-gray-300 @error('description') border-red-500 @enderror" placeholder="Décrivez la tâche en détail..." style="--tw-ring-opacity: 1; --tw-ring-color: #5680E9;" onfocus="this.style.borderColor='#5680E9'" onblur="this.style.borderColor=''">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priorité -->
                    <div>
                        <label for="priorite" class="block text-sm font-medium text-gray-700 mb-1">Priorité *</label>
                        <select name="priorite" id="priorite" required class="w-full rounded-lg border-gray-300 @error('priorite') border-red-500 @enderror" style="--tw-ring-opacity: 1; --tw-ring-color: #5680E9;" onfocus="this.style.borderColor='#5680E9'" onblur="this.style.borderColor=''">
                            <option value="low" {{ old('priorite', 'medium') === 'low' ? 'selected' : '' }}>Basse</option>
                            <option value="medium" {{ old('priorite', 'medium') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="high" {{ old('priorite') === 'high' ? 'selected' : '' }}>Haute</option>
                        </select>
                        @error('priorite')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date début -->
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut', now()->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 @error('date_debut') border-red-500 @enderror" style="--tw-ring-opacity: 1; --tw-ring-color: #5680E9;" onfocus="this.style.borderColor='#5680E9'" onblur="this.style.borderColor=''">
                        @error('date_debut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date fin -->
                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin (échéance)</label>
                        <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin') }}" class="w-full rounded-lg border-gray-300 @error('date_fin') border-red-500 @enderror" style="--tw-ring-opacity: 1; --tw-ring-color: #5680E9;" onfocus="this.style.borderColor='#5680E9'" onblur="this.style.borderColor=''">
                        @error('date_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Documents joints -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Documents joints</label>
                    <div class="p-4 border-2 border-dashed border-gray-200 rounded-xl hover:border-blue-400 transition-colors">
                        <input type="file" name="documents[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.jpg,.jpeg,.png"
                               class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 cursor-pointer">
                        <p class="text-xs text-gray-500 mt-2">PDF, Word, Excel, PowerPoint, images - Max 10 Mo par fichier, 5 fichiers max</p>
                    </div>
                    @error('documents')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('documents.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.tasks.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Annuler</a>
                    <button type="submit" class="px-6 py-2 text-white font-medium rounded-lg transition-colors" style="background: linear-gradient(135deg, #5680E9, #5AB9EA); box-shadow: 0 4px 6px -1px rgba(86, 128, 233, 0.3);" onmouseover="this.style.filter='brightness(1.1)'" onmouseout="this.style.filter=''">
                        Assigner la tâche
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
