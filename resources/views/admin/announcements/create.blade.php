<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.announcements.index') }}" 
               class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nouvelle Annonce</h1>
                <p class="text-gray-500">Créer une communication interne</p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Main Content Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">📝 Contenu</h2>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                           placeholder="Ex: Maintenance serveur prévue">
                    @error('title')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type & Priority -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            <option value="info" {{ old('type') === 'info' ? 'selected' : '' }}>ℹ️ Information</option>
                            <option value="success" {{ old('type') === 'success' ? 'selected' : '' }}>✅ Bonne nouvelle</option>
                            <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>⚠️ Attention</option>
                            <option value="urgent" {{ old('type') === 'urgent' ? 'selected' : '' }}>🚨 Urgent</option>
                            <option value="event" {{ old('type') === 'event' ? 'selected' : '' }}>📅 Événement</option>
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                            Priorité <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>Normale</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                            <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Critique (bannière)</option>
                        </select>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                        Contenu <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" id="content" rows="6" required
                              class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                              placeholder="Rédigez votre annonce...">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Targeting Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">🎯 Ciblage</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Destinataires</label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="target_type" value="all" checked
                                   class="text-green-600 focus:ring-green-500"
                                   onchange="updateTargetFields()">
                            <div>
                                <span class="font-medium text-gray-900">Tous les employés</span>
                                <p class="text-sm text-gray-500">L'annonce sera visible par tous</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="target_type" value="department" {{ old('target_type') === 'department' ? 'checked' : '' }}
                                   class="text-green-600 focus:ring-green-500"
                                   onchange="updateTargetFields()">
                            <div>
                                <span class="font-medium text-gray-900">Un département</span>
                                <p class="text-sm text-gray-500">Cibler un département spécifique</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="target_type" value="position" {{ old('target_type') === 'position' ? 'checked' : '' }}
                                   class="text-green-600 focus:ring-green-500"
                                   onchange="updateTargetFields()">
                            <div>
                                <span class="font-medium text-gray-900">Un poste</span>
                                <p class="text-sm text-gray-500">Cibler un poste spécifique</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="target_type" value="custom" {{ old('target_type') === 'custom' ? 'checked' : '' }}
                                   class="text-green-600 focus:ring-green-500"
                                   onchange="updateTargetFields()">
                            <div>
                                <span class="font-medium text-gray-900">Utilisateurs spécifiques</span>
                                <p class="text-sm text-gray-500">Sélectionner des employés individuellement</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Department Select -->
                <div id="departmentField" class="hidden">
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Département
                    </label>
                    <select name="department_id" id="department_id"
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <option value="">Sélectionner un département</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Position Select -->
                <div id="positionField" class="hidden">
                    <label for="position_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Poste
                    </label>
                    <select name="position_id" id="position_id"
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <option value="">Sélectionner un poste</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                {{ $pos->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Users Multi-Select -->
                <div id="usersField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Employés
                    </label>
                    <div class="border rounded-lg max-h-60 overflow-y-auto p-2">
                        @foreach($employees as $emp)
                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" name="target_user_ids[]" value="{{ $emp->id }}"
                                       class="rounded text-green-600 focus:ring-green-500">
                                <span class="text-gray-900">{{ $emp->name }}</span>
                                <span class="text-sm text-gray-500">{{ $emp->email }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Scheduling Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">📅 Planification</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Date de début (optionnel)
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <p class="mt-1 text-xs text-gray-500">Laissez vide pour afficher immédiatement</p>
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Date de fin (optionnel)
                        </label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <p class="mt-1 text-xs text-gray-500">Laissez vide pour afficher indéfiniment</p>
                    </div>
                </div>
            </div>

            <!-- Options Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">⚙️ Options</h2>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }}
                           class="rounded text-green-600 focus:ring-green-500">
                    <div>
                        <span class="font-medium text-gray-900">📌 Épingler en haut</span>
                        <p class="text-sm text-gray-500">L'annonce restera en haut de la liste</p>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="requires_acknowledgment" value="1" {{ old('requires_acknowledgment') ? 'checked' : '' }}
                           class="rounded text-green-600 focus:ring-green-500">
                    <div>
                        <span class="font-medium text-gray-900">✅ Exiger un accusé de réception</span>
                        <p class="text-sm text-gray-500">Les employés devront confirmer avoir lu l'annonce</p>
                    </div>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.announcements.index') }}" 
                   class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Publier l'annonce
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function updateTargetFields() {
            const targetType = document.querySelector('input[name="target_type"]:checked').value;
            
            document.getElementById('departmentField').classList.toggle('hidden', targetType !== 'department');
            document.getElementById('positionField').classList.toggle('hidden', targetType !== 'position');
            document.getElementById('usersField').classList.toggle('hidden', targetType !== 'custom');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateTargetFields);
    </script>
    @endpush
</x-layouts.admin>
