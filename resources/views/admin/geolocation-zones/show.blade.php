<x-layouts.admin>
    <x-slot name="header">
        <!-- Header moderne avec gradient -->
        <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 -mx-4 sm:-mx-6 lg:-mx-8 -mt-4 px-4 sm:px-6 lg:px-8 py-6 mb-4">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.geolocation-zones.index') }}" 
                           class="p-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/10 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl sm:text-2xl font-bold text-white">{{ $geolocationZone->name }}</h2>
                                    <p class="text-emerald-100 text-sm mt-0.5">Détails de la zone de géolocalisation</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Status badges et action -->
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="flex items-center gap-2">
                            @if($geolocationZone->is_active)
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-500/20 text-white text-sm font-medium rounded-full border border-green-400/30">
                                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-500/20 text-white text-sm font-medium rounded-full border border-gray-400/30">
                                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                    Inactive
                                </span>
                            @endif
                            @if($geolocationZone->is_default)
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500/20 text-white text-sm font-medium rounded-full border border-yellow-400/30">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    Par défaut
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('admin.geolocation-zones.edit', $geolocationZone) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white text-emerald-700 font-semibold rounded-lg hover:bg-emerald-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span class="hidden sm:inline">Modifier</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="grid gap-6 lg:grid-cols-5">
            <!-- Colonne gauche : Informations (2/5 sur desktop) -->
            <div class="lg:col-span-2 space-y-6 order-2 lg:order-1">
                <!-- Carte d'information principale -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informations générales
                        </h3>
                    </div>

                    <div class="p-5 space-y-4">
                        <!-- Description -->
                        @if($geolocationZone->description)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Description</label>
                                <p class="text-gray-800">{{ $geolocationZone->description }}</p>
                            </div>
                        @endif

                        <!-- Adresse -->
                        @if($geolocationZone->address)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Adresse</label>
                                <p class="text-gray-800 flex items-start gap-2">
                                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>{{ $geolocationZone->address }}</span>
                                </p>
                            </div>
                        @endif

                        <!-- Dates -->
                        <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Créée le</label>
                                <p class="text-gray-800 text-sm">{{ $geolocationZone->created_at->format('d/m/Y') }}</p>
                                <p class="text-gray-500 text-xs">{{ $geolocationZone->created_at->format('H:i') }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Modifiée le</label>
                                <p class="text-gray-800 text-sm">{{ $geolocationZone->updated_at->format('d/m/Y') }}</p>
                                <p class="text-gray-500 text-xs">{{ $geolocationZone->updated_at->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques GPS -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Paramétres GPS
                        </h3>
                    </div>

                    <div class="p-5">
                        <div class="space-y-3">
                            <!-- Latitude -->
                            <div class="flex items-center gap-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg p-3 border border-emerald-100">
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Latitude</p>
                                    <p class="text-base font-bold text-gray-900 font-mono truncate">{{ number_format($geolocationZone->latitude, 6) }}</p>
                                </div>
                            </div>

                            <!-- Longitude -->
                            <div class="flex items-center gap-4 bg-gradient-to-r from-teal-50 to-cyan-50 rounded-lg p-3 border border-teal-100">
                                <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Longitude</p>
                                    <p class="text-base font-bold text-gray-900 font-mono truncate">{{ number_format($geolocationZone->longitude, 6) }}</p>
                                </div>
                            </div>

                            <!-- Rayon -->
                            <div class="flex items-center gap-4 bg-gradient-to-r from-cyan-50 to-blue-50 rounded-lg p-3 border border-cyan-100">
                                <div class="w-10 h-10 bg-cyan-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Rayon</p>
                                    <p class="text-base font-bold text-gray-900">{{ $geolocationZone->radius }} <span class="text-sm font-normal text-gray-600">métres</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides (mobile) -->
                <div class="flex flex-col gap-3 lg:hidden">
                    <a href="{{ route('admin.geolocation-zones.edit', $geolocationZone) }}"
                       class="w-full px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg text-sm font-semibold hover:from-emerald-700 hover:to-teal-700 transition-all shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Modifier cette zone
                    </a>
                    <a href="{{ route('admin.geolocation-zones.index') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors text-center">
                        Retour é  la liste
                    </a>
                </div>
            </div>

            <!-- Colonne droite : Carte (3/5 sur desktop) -->
            <div class="lg:col-span-3 order-1 lg:order-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            Visualisation de la zone
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Aperé§u de la zone de pointage autorisée</p>
                    </div>

                    <div class="p-4">
                        <div id="map" class="rounded-lg border-2 border-gray-200 shadow-inner" style="height: 450px; min-height: 350px;"></div>
                        <div class="mt-3 flex items-start gap-2 text-sm text-gray-600 bg-emerald-50 p-3 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Le cercle représente la zone de <strong>{{ $geolocationZone->radius }} métres</strong> dans laquelle les employés peuvent pointer leur présence.</span>
                        </div>
                    </div>

                    <!-- Footer avec actions (desktop) -->
                    <div class="hidden lg:flex justify-between items-center px-5 py-4 bg-gray-50 border-t border-gray-100">
                        <a href="{{ route('admin.geolocation-zones.index') }}"
                           class="text-sm text-gray-600 hover:text-gray-800 transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Retour é  la liste
                        </a>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.geolocation-zones.edit', $geolocationZone) }}"
                               class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg text-sm font-semibold hover:from-emerald-700 hover:to-teal-700 transition-all shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Modifier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script nonce="{{ $cspNonce ?? '' }}" src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script nonce="{{ $cspNonce ?? '' }}">
        document.addEventListener('DOMContentLoaded', function() {
            const lat = {{ $geolocationZone->latitude }};
            const lng = {{ $geolocationZone->longitude }};
            const radius = {{ $geolocationZone->radius }};

            // Initialiser la carte
            const map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'é‚ OpenStreetMap contributors'
            }).addTo(map);

            // Marqueur avec popup personnalisé
            const popupContent = `
                <div class="text-center">
                    <strong class="text-lg">{{ $geolocationZone->name }}</strong>
                    <p class="text-gray-600 text-sm mt-1">Rayon: ${radius} m</p>
                    @if($geolocationZone->address)
                    <p class="text-gray-500 text-xs mt-1">{{ Str::limit($geolocationZone->address, 50) }}</p>
                    @endif
                </div>
            `;
            
            L.marker([lat, lng]).addTo(map)
                .bindPopup(popupContent)
                .openPopup();

            // Cercle de la zone
            L.circle([lat, lng], {
                radius: radius,
                color: '#10B981',
                fillColor: '#10B981',
                fillOpacity: 0.15,
                weight: 2
            }).addTo(map);

            // Ajuster le zoom pour voir tout le cercle
            const bounds = L.latLng(lat, lng).toBounds(radius * 2.5);
            map.fitBounds(bounds);

            // Resize map when container size changes (for responsive)
            setTimeout(() => map.invalidateSize(), 100);
            window.addEventListener('resize', () => map.invalidateSize());
        });
    </script>

    <style>
        /* Responsive map height */
        @media (max-width: 1023px) {
            #map {
                height: 350px !important;
            }
        }
        @media (max-width: 640px) {
            #map {
                height: 300px !important;
            }
        }
        
        /* Custom popup style */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            padding: 4px;
        }
        .leaflet-popup-content {
            margin: 12px 16px;
        }
    </style>
</x-layouts.admin>
