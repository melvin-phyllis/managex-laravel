<x-layouts.admin>
    @php
        $allLeaves = \App\Models\Leave::query();
        $pendingCount = (clone $allLeaves)->where('statut', 'pending')->count();
        $approvedCount = (clone $allLeaves)->where('statut', 'approved')->count();
        $rejectedCount = (clone $allLeaves)->where('statut', 'rejected')->count();
        $totalCount = $allLeaves->count();
    @endphp

    <div class="space-y-6">
        <!-- Header avec gradient -->
        <div class="relative overflow-hidden bg-gradient-to-r from-teal-600 via-cyan-600 to-blue-600 rounded-2xl shadow-xl">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <nav class="flex mb-3" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 text-sm">
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" class="text-white/70 hover:text-white transition-colors">Dashboard</a>
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-white/50 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span class="text-white font-medium">Congés</span>
                                </li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">Demandes de congés</h1>
                        <p class="text-white/80 mt-1">Gérez les demandes de congés de vos employés</p>
                    </div>
                    
                    <!-- Indicateurs rapides dans le header -->
                    <div class="flex items-center gap-3">
                        @if($pendingCount > 0)
                            <div class="px-4 py-2 bg-amber-500/90 backdrop-blur-sm rounded-xl flex items-center gap-2 shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-white font-bold">{{ $pendingCount }}</span>
                                <span class="text-white/90 text-sm">en attente</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center shadow-lg shadow-slate-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCount }}</p>
                        <p class="text-xs text-gray-500">Total demandes</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                        <p class="text-xs text-gray-500">En attente</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $approvedCount }}</p>
                        <p class="text-xs text-gray-500">Approuvés</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center shadow-lg shadow-red-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $rejectedCount }}</p>
                        <p class="text-xs text-gray-500">Rejetés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-5 py-3 border-b border-gray-100">
                <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtres de recherche
                </h3>
            </div>
            <form action="{{ route('admin.leaves.index') }}" method="GET" class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
                        <select name="employee_id" id="employee_id" class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                            <option value="">Tous les employés</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" id="statut" class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('statut') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="approved" {{ request('statut') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                            <option value="rejected" {{ request('statut') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                        </select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" id="type" class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                            <option value="">Tous les types</option>
                            <option value="conge" {{ request('type') == 'conge' ? 'selected' : '' }}>Congé payé</option>
                            <option value="maladie" {{ request('type') == 'maladie' ? 'selected' : '' }}>Arrêt maladie</option>
                            <option value="autre" {{ request('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-teal-600 to-cyan-600 text-white font-medium rounded-xl hover:from-teal-700 hover:to-cyan-700 transition-all shadow-lg shadow-teal-500/30">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Filtrer
                            </span>
                        </button>
                        @if(request()->hasAny(['employee_id', 'statut', 'type']))
                            <a href="{{ route('admin.leaves.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Leaves Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employé</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Période</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Durée</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Motif</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($leaves as $leave)
                            @php
                                $typeConfig = [
                                    'conge' => ['gradient' => 'from-blue-500 to-indigo-600', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                    'maladie' => ['gradient' => 'from-red-500 to-rose-600', 'bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                                    'autre' => ['gradient' => 'from-gray-500 to-slate-600', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                ];
                                $config = $typeConfig[$leave->type] ?? $typeConfig['autre'];
                            @endphp
                            <tr class="hover:bg-teal-50/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @if($leave->user->avatar)
                                            <img class="h-10 w-10 rounded-xl object-cover ring-2 ring-white shadow-md" src="{{ Storage::url($leave->user->avatar) }}" alt="{{ $leave->user->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center ring-2 ring-white shadow-md">
                                                <span class="text-white font-bold text-xs">{{ strtoupper(substr($leave->user->name, 0, 2)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $leave->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $leave->user->poste ?? 'Employé' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $config['gradient'] }} flex items-center justify-center shadow-sm">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                                            </svg>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                            {{ $leave->type_label }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $leave->date_debut->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                        {{ $leave->date_fin->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-teal-100 text-teal-800">
                                        {{ $leave->duree }} jour{{ $leave->duree > 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $leave->motif }}">
                                        {{ Str::limit($leave->motif, 40) ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'dot' => 'bg-amber-500', 'label' => 'En attente'],
                                            'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'dot' => 'bg-emerald-500', 'label' => 'Approuvé'],
                                            'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'dot' => 'bg-red-500', 'label' => 'Rejeté'],
                                        ];
                                        $status = $statusConfig[$leave->statut] ?? $statusConfig['pending'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $status['bg'] }} {{ $status['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $status['dot'] }}"></span>
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.leaves.show', $leave) }}" 
                                           class="p-2 text-gray-500 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors" title="Voir les détails">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @if($leave->statut === 'pending')
                                            <form action="{{ route('admin.leaves.approve', $leave) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="p-2 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" 
                                                        title="Approuver">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.leaves.reject', $leave) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                                        title="Rejeter">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-teal-100 to-cyan-100 flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Aucune demande de congé trouvée</p>
                                        <p class="text-gray-400 text-sm mt-1">Les demandes de congés apparaîtront ici</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($leaves->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $leaves->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
