<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
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
                                <li><a href="{{ route('admin.announcements.index') }}" class="text-white/70 hover:text-white text-sm">Annonces</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Modifier</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            Modifier l'Annonce
                        </h1>
                        <p class="text-white/80 mt-2">{{ $announcement->title }}</p>
                    </div>
                    <a href="{{ route('admin.announcements.index') }}" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Main Content Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6 animate-fade-in-up animation-delay-100">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(86, 128, 233, 0.15);">
                        <svg class="w-4 h-4" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    Contenu
                </h2>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $announcement->title) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
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
                                class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                            <option value="info" {{ old('type', $announcement->type) === 'info' ? 'selected' : '' }}>ℹ️ Information</option>
                            <option value="success" {{ old('type', $announcement->type) === 'success' ? 'selected' : '' }}>✅ Bonne nouvelle</option>
                            <option value="warning" {{ old('type', $announcement->type) === 'warning' ? 'selected' : '' }}>⚠️ Attention</option>
                            <option value="urgent" {{ old('type', $announcement->type) === 'urgent' ? 'selected' : '' }}>🚨 Urgent</option>
                            <option value="event" {{ old('type', $announcement->type) === 'event' ? 'selected' : '' }}>📅 Événement</option>
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                            Priorité <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                                class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                            <option value="normal" {{ old('priority', $announcement->priority) === 'normal' ? 'selected' : '' }}>Normale</option>
                            <option value="high" {{ old('priority', $announcement->priority) === 'high' ? 'selected' : '' }}>Haute</option>
                            <option value="critical" {{ old('priority', $announcement->priority) === 'critical' ? 'selected' : '' }}>Critique (bannière)</option>
                        </select>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                        Contenu <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" id="content" rows="6" required
                              class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">{{ old('content', $announcement->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Targeting Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6 animate-fade-in-up animation-delay-200">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(136, 96, 208, 0.15);">
                        <svg class="w-4 h-4" style="color: #8860D0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    Ciblage
                </h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Destinataires</label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-purple-50/50 transition-colors">
                            <input type="radio" name="target_type" value="all" 
                                   {{ old('target_type', $announcement->target_type) === 'all' ? 'checked' : '' }}
                                   class="focus:ring-indigo-500" style="color: #5680E9;" onchange="updateTargetFields()">
                            <span class="font-medium text-gray-900">Tous les employés</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-purple-50/50 transition-colors">
                            <input type="radio" name="target_type" value="department" 
                                   {{ old('target_type', $announcement->target_type) === 'department' ? 'checked' : '' }}
                                   class="focus:ring-indigo-500" style="color: #5680E9;" onchange="updateTargetFields()">
                            <span class="font-medium text-gray-900">Un département</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-purple-50/50 transition-colors">
                            <input type="radio" name="target_type" value="position" 
                                   {{ old('target_type', $announcement->target_type) === 'position' ? 'checked' : '' }}
                                   class="focus:ring-indigo-500" style="color: #5680E9;" onchange="updateTargetFields()">
                            <span class="font-medium text-gray-900">Un poste</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-purple-50/50 transition-colors">
                            <input type="radio" name="target_type" value="custom" 
                                   {{ old('target_type', $announcement->target_type) === 'custom' ? 'checked' : '' }}
                                   class="focus:ring-indigo-500" style="color: #5680E9;" onchange="updateTargetFields()">
                            <span class="font-medium text-gray-900">Utilisateurs spécifiques</span>
                        </label>
                    </div>
                </div>

                <div id="departmentField" class="{{ $announcement->target_type !== 'department' ? 'hidden' : '' }}">
                    <select name="department_id" class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Sélectionner un département</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $announcement->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="positionField" class="{{ $announcement->target_type !== 'position' ? 'hidden' : '' }}">
                    <select name="position_id" class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Sélectionner un poste</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id', $announcement->position_id) == $pos->id ? 'selected' : '' }}>
                                {{ $pos->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="usersField" class="{{ $announcement->target_type !== 'custom' ? 'hidden' : '' }}">
                    <div class="border border-gray-200 rounded-xl max-h-60 overflow-y-auto p-2">
                        @php $selectedUsers = $announcement->target_user_ids ?? []; @endphp
                        @foreach($employees as $emp)
                            <label class="flex items-center gap-3 p-2 hover:bg-purple-50/50 rounded-lg cursor-pointer transition-colors">
                                <input type="checkbox" name="target_user_ids[]" value="{{ $emp->id }}"
                                       {{ in_array($emp->id, $selectedUsers) ? 'checked' : '' }}
                                       class="rounded focus:ring-indigo-500" style="color: #5680E9;">
                                <span class="text-gray-900">{{ $emp->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Scheduling Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6 animate-fade-in-up animation-delay-300">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(90, 185, 234, 0.15);">
                        <svg class="w-4 h-4" style="color: #5AB9EA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    Planification
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $announcement->start_date?->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $announcement->end_date?->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Options Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 animate-fade-in-up animation-delay-400">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 206, 235, 0.15);">
                        <svg class="w-4 h-4" style="color: #84CEEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    Options
                </h2>
                <label class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-purple-50/50 transition-colors">
                    <input type="checkbox" name="is_pinned" value="1" 
                           {{ old('is_pinned', $announcement->is_pinned) ? 'checked' : '' }}
                           class="rounded focus:ring-indigo-500" style="color: #5680E9;">
                    <span class="font-medium text-gray-900">📌 Épingler en haut</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-purple-50/50 transition-colors">
                    <input type="checkbox" name="requires_acknowledgment" value="1" 
                           {{ old('requires_acknowledgment', $announcement->requires_acknowledgment) ? 'checked' : '' }}
                           class="rounded focus:ring-indigo-500" style="color: #5680E9;">
                    <span class="font-medium text-gray-900">✅ Exiger un accusé de réception</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.announcements.index') }}" 
                   class="px-6 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 text-white font-semibold rounded-xl shadow-lg transition-all" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}">
        function updateTargetFields() {
            const targetType = document.querySelector('input[name="target_type"]:checked').value;
            document.getElementById('departmentField').classList.toggle('hidden', targetType !== 'department');
            document.getElementById('positionField').classList.toggle('hidden', targetType !== 'position');
            document.getElementById('usersField').classList.toggle('hidden', targetType !== 'custom');
        }
        document.addEventListener('DOMContentLoaded', updateTargetFields);
    </script>
    @endpush
</x-layouts.admin>
