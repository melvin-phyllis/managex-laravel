<x-layouts.admin>
    <div class="min-h-screen">
        <!-- Header -->
        <div class="rounded-2xl lg:rounded-3xl mx-4 sm:mx-6 lg:mx-8 mt-4 sm:mt-6 p-6 sm:p-8 text-white shadow-xl relative overflow-hidden" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2 blur-xl"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 sm:p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold">Zones de Géolocalisation</h1>
                                <p class="text-blue-100 text-sm sm:text-base mt-1">Gérez les périmètres autorisés pour le pointage</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.geolocation-zones.create') }}" 
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white font-semibold rounded-xl hover:bg-blue-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5" style="color: #5680E9;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="hidden sm:inline">Nouvelle zone</span>
                        <span class="sm:hidden">Ajouter</span>
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mt-6">
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl p-3 sm:p-4">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="p-2 bg-white/20 rounded-lg">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xl sm:text-2xl font-bold">{{ $zones->total() }}</p>
                                <p class="text-xs sm:text-sm text-blue-100">Zones</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl p-3 sm:p-4">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="p-2 bg-green-400/30 rounded-lg">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xl sm:text-2xl font-bold">{{ $zones->where('is_active', true)->count() }}</p>
                                <p class="text-xs sm:text-sm text-blue-100">Actives</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl p-3 sm:p-4">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="p-2 rounded-lg" style="background-color: rgba(136, 96, 208, 0.3);">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xl sm:text-2xl font-bold">{{ $zones->where('is_default', true)->count() }}</p>
                                <p class="text-xs sm:text-sm text-blue-100">Par défaut</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl p-3 sm:p-4">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="p-2 bg-blue-400/30 rounded-lg">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xl sm:text-2xl font-bold">{{ $zones->sum('presences_count') }}</p>
                                <p class="text-xs sm:text-sm text-blue-100">Pointages</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <!-- Info box -->
            <div class="border rounded-xl p-4 mb-6" style="background: linear-gradient(90deg, #5680E920, #84CEEB10); border-color: #5680E940;">
                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <div class="p-2 rounded-lg" style="background-color: #5680E920;">
                            <svg class="h-5 w-5" style="color: #5680E9;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold" style="color: #5680E9;">Comment ça fonctionne ?</h3>
                        <p class="text-sm mt-1" style="color: #5680E9CC;">
                            Les zones de géolocalisation définissent les périmètres autorisés pour le pointage des employés.
                            Seuls les pointages effectués dans ces zones seront marqués comme "dans la zone".
                        </p>
                    </div>
                </div>
            </div>

            @if($zones->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Zone</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Coordonnées</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rayon</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pointages</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($zones as $zone)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 rounded-xl" style="background-color: #5680E920;">
                                                <svg class="w-5 h-5" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold text-gray-900">{{ $zone->name }}</span>
                                                    @if($zone->is_default)
                                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                                                            Par défaut
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($zone->address)
                                                    <p class="text-sm text-gray-500 mt-0.5 max-w-xs truncate">{{ $zone->address }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="p-1.5 bg-gray-100 rounded-lg">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm text-gray-600 font-mono">
                                                {{ number_format($zone->latitude, 4) }}, {{ number_format($zone->longitude, 4) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium" style="background-color: #8860D020; color: #8860D0;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                            </svg>
                                            {{ $zone->radius }} m
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium" style="background-color: #5680E920; color: #5680E9;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $zone->presences_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($zone->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-sm font-medium">
                                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium">
                                                <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.geolocation-zones.show', $zone) }}"
                                               class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                                               title="Voir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.geolocation-zones.edit', $zone) }}"
                                               class="p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors"
                                               title="Modifier">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            @if(!$zone->is_default)
                                                <form action="{{ route('admin.geolocation-zones.set-default', $zone) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="p-2 text-amber-500 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-colors"
                                                            title="Définir par défaut">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.geolocation-zones.destroy', $zone) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette zone ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                                        title="Supprimer">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-4">
                    @foreach($zones as $zone)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <!-- Card Header -->
                            <div class="p-4 border-b border-gray-100">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2.5 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="font-bold text-gray-900">{{ $zone->name }}</h3>
                                                @if($zone->is_default)
                                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                                                        Par défaut
                                                    </span>
                                                @endif
                                            </div>
                                            @if($zone->address)
                                                <p class="text-sm text-gray-500 mt-0.5">{{ Str::limit($zone->address, 40) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($zone->is_active)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-700 rounded-lg text-xs font-medium">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-medium">
                                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Stats -->
                            <div class="grid grid-cols-3 gap-px bg-gray-100">
                                <div class="bg-white p-3 text-center">
                                    <p class="text-xs text-gray-500 mb-1">Coordonnées</p>
                                    <p class="text-xs font-mono text-gray-700">{{ number_format($zone->latitude, 3) }}, {{ number_format($zone->longitude, 3) }}</p>
                                </div>
                                <div class="bg-white p-3 text-center">
                                    <p class="text-xs text-gray-500 mb-1">Rayon</p>
                                    <p class="text-sm font-semibold" style="color: #8860D0;">{{ $zone->radius }} m</p>
                                </div>
                                <div class="bg-white p-3 text-center">
                                    <p class="text-xs text-gray-500 mb-1">Pointages</p>
                                    <p class="text-sm font-semibold" style="color: #5680E9;">{{ $zone->presences_count }}</p>
                                </div>
                            </div>

                            <!-- Card Actions -->
                            <div class="p-3 bg-gray-50 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.geolocation-zones.show', $zone) }}"
                                       class="flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Voir
                                    </a>
                                    <a href="{{ route('admin.geolocation-zones.edit', $zone) }}"
                                       class="flex items-center gap-1.5 px-3 py-2 text-white text-sm font-medium rounded-lg transition-colors" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Modifier
                                    </a>
                                </div>
                                <div class="flex items-center gap-1">
                                    @if(!$zone->is_default)
                                        <form action="{{ route('admin.geolocation-zones.set-default', $zone) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                                    title="Définir par défaut">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.geolocation-zones.destroy', $zone) }}" method="POST"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette zone ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($zones->hasPages())
                    <div class="mt-6">
                        {{ $zones->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6" style="background: linear-gradient(135deg, #5680E920, #84CEEB20);">
                            <svg class="w-10 h-10" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Aucune zone de géolocalisation</h3>
                        <p class="text-gray-500 mb-6">
                            Créez une zone pour activer la vérification de localisation lors du pointage des employés.
                        </p>
                        <a href="{{ route('admin.geolocation-zones.create') }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Créer ma première zone
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
