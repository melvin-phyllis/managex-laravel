<x-layouts.admin>
    <div class="space-y-6" x-data="taskManager()">
        <!-- Header amélioré -->
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
                                <li><span class="text-white text-sm font-medium">Taches</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            Gestion des Taches
                        </h1>
                        <p class="text-white/80 mt-2">Assignation, suivi et validation des taches collaborateurs</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Toggle View -->
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-1 flex">
                            <button @click="viewMode = 'table'" 
                                    :class="viewMode === 'table' ? 'bg-white' : 'text-white hover:bg-white/20'"
                                    :style="viewMode === 'table' ? 'color: #5680E9;' : ''"
                                    class="px-3 py-2 rounded-lg font-medium text-sm transition-all flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                Liste
                            </button>
                            <button @click="viewMode = 'kanban'" 
                                    :class="viewMode === 'kanban' ? 'bg-white' : 'text-white hover:bg-white/20'"
                                    :style="viewMode === 'kanban' ? 'color: #5680E9;' : ''"
                                    class="px-3 py-2 rounded-lg font-medium text-sm transition-all flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                                Kanban
                            </button>
                            <button @click="viewMode = 'calendar'; $nextTick(() => initCalendar())" 
                                    :class="viewMode === 'calendar' ? 'bg-white' : 'text-white hover:bg-white/20'"
                                    :style="viewMode === 'calendar' ? 'color: #5680E9;' : ''"
                                    class="px-3 py-2 rounded-lg font-medium text-sm transition-all flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Calendrier
                            </button>
                        </div>
                        <a href="{{ route('admin.tasks.create') }}" class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nouvelle tache
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards (optimisé: données depuis le contrôleur) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 animate-fade-in-up animation-delay-100">
            @php
                $stats = [
                    ['label' => 'Total', 'value' => $taskStats->total ?? 0, 'color' => '#6B7280', 'bg' => 'rgba(107, 114, 128, 0.1)', 'icon' => 'clipboard-list'],
                    ['label' => 'En attente', 'value' => $taskStats->pending_count ?? 0, 'color' => '#5680E9', 'bg' => 'rgba(86, 128, 233, 0.1)', 'icon' => 'clock'],
                    ['label' => 'En cours', 'value' => $taskStats->in_progress_count ?? 0, 'color' => '#5AB9EA', 'bg' => 'rgba(90, 185, 234, 0.15)', 'icon' => 'play'],
                    ['label' => 'Terminées', 'value' => $taskStats->completed_count ?? 0, 'color' => '#84CEEB', 'bg' => 'rgba(132, 206, 235, 0.2)', 'icon' => 'check'],
                    ['label' => 'Validées', 'value' => $taskStats->validated_count ?? 0, 'color' => '#5AB9EA', 'bg' => 'rgba(90, 185, 234, 0.2)', 'icon' => 'badge-check'],
                    ['label' => 'En retard', 'value' => $taskStats->overdue_count ?? 0, 'color' => '#8860D0', 'bg' => 'rgba(136, 96, 208, 0.15)', 'icon' => 'exclamation'],
                ];
            @endphp
            @foreach($stats as $stat)
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $stat['bg'] }};">
                            @if($stat['icon'] === 'clipboard-list')
  <svg class="w-5 h-5" style="color: {{ $stat['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            @elseif($stat['icon'] === 'clock')
                                <svg class="w-5 h-5" style="color: {{ $stat['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($stat['icon'] === 'play')
                                <svg class="w-5 h-5" style="color: {{ $stat['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($stat['icon'] === 'check')
                                <svg class="w-5 h-5" style="color: {{ $stat['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @elseif($stat['icon'] === 'badge-check')
                                <svg class="w-5 h-5" style="color: {{ $stat['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            @elseif($stat['icon'] === 'exclamation')
                                <svg class="w-5 h-5" style="color: {{ $stat['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            @endif
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ $stat['value'] }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- Filter Bar -->
        <x-filter-bar :hasActiveFilters="request()->hasAny(['search', 'user_id', 'statut', 'priorite'])" class="animate-fade-in-up animation-delay-200">
            <x-slot:filters>
                <!-- Search -->
                <div class="flex-1 min-w-[200px] relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Rechercher une tache..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 text-sm" style="--tw-ring-color: #5680E9; border-color: #5680E9 on focus;">
                </div>

                <!-- Employé -->
                <div class="w-full sm:w-auto">
                    <select name="user_id" class="w-full sm:w-48 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                        <option value="">Tous les employés</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Statut -->
                <div class="w-full sm:w-auto">
                    <select name="statut" class="w-full sm:w-40 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                        <option value="">Tous statuts</option>
                        <option value="pending" {{ request('statut') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="approved" {{ request('statut') == 'approved' ? 'selected' : '' }}>En cours</option>
                        <option value="completed" {{ request('statut') == 'completed' ? 'selected' : '' }}>Terminée (à valider)</option>
                        <option value="validated" {{ request('statut') == 'validated' ? 'selected' : '' }}>Validée</option>
                        <option value="rejected" {{ request('statut') == 'rejected' ? 'selected' : '' }}>Rejetée</option>
                    </select>
                </div>

                <!-- Priorité -->
                <div class="w-full sm:w-auto">
                    <select name="priorite" class="w-full sm:w-40 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                        <option value="">Toutes priorités</option>
                        <option value="low" {{ request('priorite') == 'low' ? 'selected' : '' }}>Basse</option>
                        <option value="medium" {{ request('priorite') == 'medium' ? 'selected' : '' }}>Moyenne</option>
                        <option value="high" {{ request('priorite') == 'high' ? 'selected' : '' }}>Haute</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2.5 text-white font-medium rounded-xl transition-colors shadow-lg flex items-center" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filtrer
                    </button>
                    @if(request()->hasAny(['search', 'user_id', 'statut', 'priorite']))
                        <a href="{{ route('admin.tasks.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors flex items-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </x-slot:filters>

            <x-slot:activeFilters>
                @if(request('search'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                        Recherche: {{ request('search') }}
                    </span>
                @endif
                @if(request('user_id'))
                    @php $emp = $employees->find(request('user_id')); @endphp
                    @if($emp)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            Employé: {{ $emp->name }}
                        </span>
                    @endif
                @endif
                @if(request('statut'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-100">
                        Statut: {{ request('statut') }}
                    </span>
                @endif
                @if(request('priorite'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                        Priorité: {{ request('priorite') }}
                    </span>
                @endif
            </x-slot:activeFilters>
        </x-filter-bar>

        <!-- Kanban View -->
        <div x-show="viewMode === 'kanban'" x-cloak class="animate-fade-in-up" x-data="kanbanBoard()">
            <!-- Toast notification -->
            <div x-show="toast.show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'"
                 class="fixed bottom-4 right-4 text-white px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2">
                <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="toast.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span x-text="toast.message"></span>
            </div>

            <!-- Help text -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-3 mb-4 flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm text-indigo-700">Glissez-déposez les taches entre les colonnes pour changer leur statut</p>
            </div>

            @php
                // $kanbanTasks passé depuis le contrôleur (optimisé)
                $columns = [
                    'pending' => ['title' => 'En attente', 'color' => 'amber', 'icon' => 'clock'],
                    'approved' => ['title' => 'En cours', 'color' => 'blue', 'icon' => 'play'],
                    'completed' => ['title' => 'Terminées', 'color' => 'purple', 'icon' => 'check'],
                    'validated' => ['title' => 'Validées', 'color' => 'emerald', 'icon' => 'badge-check'],
                    'rejected' => ['title' => 'Rejetées', 'color' => 'red', 'icon' => 'x-circle'],
                ];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($columns as $status => $config)
                    <div class="bg-gray-50 rounded-xl p-3 min-h-[400px] kanban-column transition-all"
                         data-status="{{ $status }}"
                         @dragover.prevent="onDragOver($event, '{{ $status }}')"
                         @dragleave="onDragLeave($event, '{{ $status }}')"
                         @drop="onDrop($event, '{{ $status }}')"
                         :class="{ 'ring-2 ring-indigo-400 ring-offset-2 bg-indigo-50': dragOverColumn === '{{ $status }}' }">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-{{ $config['color'] }}-100 rounded-lg flex items-center justify-center">
                                    @if($config['icon'] === 'clock')
                                        <svg class="w-4 h-4 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif($config['icon'] === 'play')
                                        <svg class="w-4 h-4 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                    @elseif($config['icon'] === 'check')
                                        <svg class="w-4 h-4 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($config['icon'] === 'badge-check')
                                        <svg class="w-4 h-4 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    @elseif($config['icon'] === 'x-circle')
                                        <svg class="w-4 h-4 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-gray-700 text-sm">{{ $config['title'] }}</h3>
                            </div>
                            <span class="kanban-count-{{ $status }} bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-700 text-xs font-bold px-2 py-0.5 rounded-full">
                                {{ $kanbanTasks->get($status)?->count() ?? 0 }}
                            </span>
                        </div>
                        <div class="space-y-3 kanban-tasks min-h-[100px]" data-status="{{ $status }}">
                            @foreach($kanbanTasks->get($status, collect()) as $task)
                                <div class="kanban-card bg-white rounded-lg p-3 shadow-sm border border-gray-100 hover:shadow-md transition-all cursor-grab active:cursor-grabbing group"
                                     draggable="true"
                                     data-task-id="{{ $task->id }}"
                                     data-task-status="{{ $task->statut }}"
                                     @dragstart="onDragStart($event, {{ $task->id }}, '{{ $task->statut }}')"
                                     @dragend="onDragEnd($event)"
                                     :class="{ 'opacity-50 scale-95': draggingTaskId === {{ $task->id }} }">
                                    <!-- Drag handle indicator -->
                                    <div class="flex items-center justify-center mb-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="flex gap-0.5">
                                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="font-medium text-gray-900 text-sm line-clamp-2">{{ $task->titre }}</h4>
                                        <span class="flex-shrink-0 px-1.5 py-0.5 text-[10px] font-bold rounded {{ $task->priorite === 'high' ? 'bg-red-100 text-red-700' : ($task->priorite === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                            {{ strtoupper(substr($task->priorite, 0, 1)) }}
                                        </span>
                                    </div>
                                    @if($task->description)
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit($task->description, 60) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between mt-3">
                                        <div class="flex items-center gap-2">
                                            @if($task->user->avatar)
                                                <img class="w-6 h-6 rounded-full object-cover ring-2 ring-white" src="{{ avatar_url($task->user->avatar) }}" alt="{{ $task->user->name }}">
                                            @else
                                                <div class="w-6 h-6 rounded-full flex items-center justify-center ring-2 ring-white" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                                    <span class="text-white font-bold text-[10px]">{{ strtoupper(substr($task->user->name, 0, 2)) }}</span>
                                                </div>
                                            @endif
                                            <span class="text-xs text-gray-600">{{ Str::limit($task->user->name, 12) }}</span>
                                        </div>
                                        @if($task->date_fin)
                                            <span class="text-[10px] {{ $task->date_fin->isPast() && !in_array($task->statut, ['validated', 'completed']) ? 'text-red-600 font-bold' : 'text-gray-400' }}">
                                                {{ $task->date_fin->format('d/m') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1 mt-2">
                                        <div class="h-1 rounded-full transition-all" style="width: {{ $task->progression }}%; background-color: {{ $task->progression < 30 ? '#8860D0' : ($task->progression < 70 ? '#84CEEB' : '#5AB9EA') }};"></div>
                                    </div>
                                    <!-- Actions on hover -->
                                    <div class="hidden group-hover:flex items-center justify-end gap-1 mt-2 pt-2 border-t border-gray-100">
                                        <a href="{{ route('admin.tasks.show', $task) }}" class="p-1 text-gray-400 hover:text-blue-600 rounded" @click.stop>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('admin.tasks.edit', $task) }}" class="p-1 text-gray-400 hover:text-amber-600 rounded" @click.stop>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                            @if(($kanbanTasks->get($status)?->count() ?? 0) === 0)
                                <div class="kanban-empty text-center py-8 text-gray-400 text-sm">
                                    <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Déposez une tache ici
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Calendar View -->
        <div x-show="viewMode === 'calendar'" x-cloak class="animate-fade-in-up">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <div id="tasksCalendar" class="min-h-[600px]"></div>
            </div>
        </div>

        <!-- Table View -->
        <div x-show="viewMode === 'table'" class="animate-fade-in-up">
        <x-data-table>
            <x-slot:header>
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tache</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employé</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">Progression</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Priorité</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">échéance</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </x-slot:header>

            <x-slot:body>
                @forelse($tasks as $task)
                    <tr class="hover:bg-purple-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg flex items-center justify-center flex-shrink-0 border border-indigo-100">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $task->titre }}</p>
                                    <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ Str::limit($task->description, 50) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-8 w-8">
                                    @if($task->user->avatar)
                                        <img class="h-8 w-8 rounded-full object-cover ring-2 ring-white shadow-sm" src="{{ avatar_url($task->user->avatar) }}" alt="{{ $task->user->name }}">
                                    @else
                                        <div class="h-8 w-8 rounded-full flex items-center justify-center ring-2 ring-white shadow-sm" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                            <span class="text-white font-bold text-xs">{{ strtoupper(substr($task->user->name, 0, 2)) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ $task->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="width: {{ $task->progression }}%; background-color: {{ $task->progression < 30 ? '#8860D0' : ($task->progression < 70 ? '#84CEEB' : '#5AB9EA') }};"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-700 w-8 text-right">{{ $task->progression }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-status-badge :status="$task->priorite" type="priority" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-status-badge :status="$task->statut" type="task" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($task->date_fin && $task->date_fin->isPast() && !in_array($task->statut, ['validated', 'completed']))
                                <div class="flex items-center gap-1.5 text-red-600 bg-red-50 px-2 py-1 rounded-lg w-fit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-bold">{{ $task->date_fin->format('d/m/Y') }}</span>
                                </div>
                            @elseif($task->date_fin)
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-sm">{{ $task->date_fin->format('d/m/Y') }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.tasks.show', $task) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Voir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.tasks.edit', $task) }}" class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                
                                @if($task->statut === 'pending')
                                    <form action="{{ route('admin.tasks.approve', $task) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Approuver">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                @elseif($task->statut === 'completed')
                                    <form action="{{ route('admin.tasks.validate', $task) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded-lg transition-colors" title="Valider">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    </form>
                                @endif

                                <button type="button" 
                                        @click="confirmDelete('{{ route('admin.tasks.destroy', $task) }}')"
                                        class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                        title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="text-lg font-medium text-gray-900">Aucune tache trouvée</p>
                                <p class="text-sm text-gray-500 mt-1">Modifiez vos filtres ou assignez une nouvelle tache.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>

            <x-slot:pagination>
                {{ $tasks->links() }}
            </x-slot:pagination>
        </x-data-table>
        </div>

    <script nonce="{{ $cspNonce ?? '' }}">
        function taskManager() {
            return {
                viewMode: localStorage.getItem('taskViewMode') || 'table',
                selected: [],
                get selectedCount() {
                    return this.selected.length;
                },
                
                // Watch viewMode changes
                init() {
                    this.$watch('viewMode', (value) => {
                        localStorage.setItem('taskViewMode', value);
                    });
                },
                
                // Delete Modal Logic
                showDeleteModal: false,
                deleteUrl: '',
                confirmDelete(url) {
                    this.deleteUrl = url;
                    this.showDeleteModal = true;
                }
            }
        }

        function kanbanBoard() {
            return {
                draggingTaskId: null,
                draggingFromStatus: null,
                dragOverColumn: null,
                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                onDragStart(event, taskId, currentStatus) {
                    this.draggingTaskId = taskId;
                    this.draggingFromStatus = currentStatus;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', taskId);
                    
                    // Add visual feedback
                    setTimeout(() => {
                        event.target.classList.add('opacity-50', 'scale-95');
                    }, 0);
                },

                onDragEnd(event) {
                    this.draggingTaskId = null;
                    this.draggingFromStatus = null;
                    this.dragOverColumn = null;
                    event.target.classList.remove('opacity-50', 'scale-95');
                },

                onDragOver(event, status) {
                    event.preventDefault();
                    this.dragOverColumn = status;
                },

                onDragLeave(event, status) {
                    // Only clear if we're leaving the column entirely
                    if (!event.currentTarget.contains(event.relatedTarget)) {
                        this.dragOverColumn = null;
                    }
                },

                async onDrop(event, newStatus) {
                    event.preventDefault();
                    this.dragOverColumn = null;
                    
                    const taskId = event.dataTransfer.getData('text/plain');
                    const oldStatus = this.draggingFromStatus;
                    
                    if (!taskId || oldStatus === newStatus) {
                        return;
                    }

                    // Optimistic UI update
                    const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
                    const targetColumn = document.querySelector(`.kanban-tasks[data-status="${newStatus}"]`);
                    const sourceColumn = document.querySelector(`.kanban-tasks[data-status="${oldStatus}"]`);
                    
                    if (taskCard && targetColumn) {
                        // Move the card visually
                        targetColumn.appendChild(taskCard);
                        taskCard.dataset.taskStatus = newStatus;
                        
                        // Update counts
                        this.updateColumnCount(oldStatus, -1);
                        this.updateColumnCount(newStatus, 1);
                        
                        // Hide empty message in target
                        const targetEmpty = targetColumn.querySelector('.kanban-empty');
                        if (targetEmpty) targetEmpty.style.display = 'none';
                        
                        // Show empty message in source if needed
                        if (sourceColumn && sourceColumn.querySelectorAll('.kanban-card').length === 0) {
                            let emptyDiv = sourceColumn.querySelector('.kanban-empty');
                            if (!emptyDiv) {
                                emptyDiv = document.createElement('div');
                                emptyDiv.className = 'kanban-empty text-center py-8 text-gray-400 text-sm';
                                emptyDiv.innerHTML = '<svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Déposez une tache ici';
                                sourceColumn.appendChild(emptyDiv);
                            }
                            emptyDiv.style.display = 'block';
                        }
                    }

                    // Send to server
                    try {
                        const response = await fetch(`{{ url('/admin/tasks') }}/${taskId}/update-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ statut: newStatus })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.showToast('Statut mis à jour avec succés', 'success');
                        } else {
                            // Revert on error
                            this.revertMove(taskCard, sourceColumn, oldStatus, newStatus);
                            this.showToast(data.message || 'Erreur lors de la mise à jour', 'error');
                        }
                    } catch (error) {
                        console.error('Error updating task:', error);
                        // Revert on error
                        this.revertMove(taskCard, sourceColumn, oldStatus, newStatus);
                        this.showToast('Erreur de connexion', 'error');
                    }
                },

                revertMove(taskCard, sourceColumn, oldStatus, newStatus) {
                    if (taskCard && sourceColumn) {
                        sourceColumn.appendChild(taskCard);
                        taskCard.dataset.taskStatus = oldStatus;
                        this.updateColumnCount(oldStatus, 1);
                        this.updateColumnCount(newStatus, -1);
                    }
                },

                updateColumnCount(status, delta) {
                    const countEl = document.querySelector(`.kanban-count-${status}`);
                    if (countEl) {
                        const currentCount = parseInt(countEl.textContent) || 0;
                        countEl.textContent = Math.max(0, currentCount + delta);
                    }
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                }
            }
        }
    </script>
    
    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showDeleteModal = false"></div>

        <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Confirmer la suppression</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer cette tache ? Cette action est irréversible.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form :action="deleteUrl" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                            Supprimer
                        </button>
                    </form>
                    <button type="button" 
                            @click="showDeleteModal = false"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
    <script nonce="{{ $cspNonce ?? '' }}" src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script nonce="{{ $cspNonce ?? '' }}">
        let calendarInstance = null;
        
        function initCalendar() {
            if (calendarInstance) {
                calendarInstance.render();
                return;
            }
            
            const calendarEl = document.getElementById('tasksCalendar');
            if (!calendarEl) return;
            
            const tasks = @json($tasks->items());
            const events = tasks.map(task => {
                const priorityColors = {
                    high: { bg: '#8860D0', border: '#7c3aed' },
                    medium: { bg: '#84CEEB', border: '#5AB9EA' },
                    low: { bg: '#6B7280', border: '#4b5563' }
                };
                const statusColors = {
                    pending: { bg: '#5680E9', border: '#4c73d2' },
                    approved: { bg: '#5AB9EA', border: '#3b82f6' },
                    in_progress: { bg: '#5AB9EA', border: '#3b82f6' },
                    completed: { bg: '#84CEEB', border: '#5AB9EA' },
                    validated: { bg: '#5AB9EA', border: '#3b82f6' },
                    rejected: { bg: '#8860D0', border: '#7c3aed' }
                };
                const colors = statusColors[task.statut] || priorityColors[task.priorite] || { bg: '#6b7280', border: '#4b5563' };
                
                return {
                    id: task.id,
                    title: task.titre,
                    start: task.date_debut || task.created_at,
                    end: task.date_fin,
                    backgroundColor: colors.bg,
                    borderColor: colors.border,
                    extendedProps: {
                        status: task.statut,
                        priority: task.priorite,
                        employee: task.user?.name,
                        progression: task.progression
                    }
                };
            });
            
            calendarInstance = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    list: 'Liste'
                },
                events: events,
                eventClick: function(info) {
                    window.location.href = '/admin/tasks/' + info.event.id;
                },
                eventDidMount: function(info) {
                    const props = info.event.extendedProps;
                    let tooltip = info.event.title;
                    if (props.employee) tooltip += '\n👤 ' + props.employee;
                    if (props.progression !== undefined) tooltip += '\n📊 ' + props.progression + '%';
                    info.el.title = tooltip;
                },
                height: 'auto',
                dayMaxEvents: 3,
                moreLinkText: function(n) { return '+' + n + ' autres'; },
                noEventsText: 'Aucune tache'
            });
            
            calendarInstance.render();
        }
        
        // Initialize if calendar view is default
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('taskViewMode') === 'calendar') {
                setTimeout(initCalendar, 100);
            }
        });
    </script>
    @endpush
</x-layouts.admin>
