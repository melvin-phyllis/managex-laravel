<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.geolocation-zones.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $geolocationZone->name }}
            </h2>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Info zone -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-2xl font-bold text-gray-900">{{ $geolocationZone->name }}</h3>
                                @if($geolocationZone->is_default)
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Par défaut</span>
                                @endif
                                @if(!$geolocationZone->is_active)
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Inactive</span>
                                @endif
                            </div>
                            @if($geolocationZone->description)
                                <p class="text-gray-600 mb-2">{{ $geolocationZone->description }}</p>
                            @endif
                            @if($geolocationZone->address)
                                <p class="text-sm text-gray-500 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $geolocationZone->address }}
                                </p>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.geolocation-zones.edit', $geolocationZone) }}"
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Modifier
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 text-center bg-gray-50 rounded-lg p-4">
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($geolocationZone->latitude, 6) }}</p>
                            <p class="text-sm text-gray-500">Latitude</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($geolocationZone->longitude, 6) }}</p>
                            <p class="text-sm text-gray-500">Longitude</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $geolocationZone->radius }} m</p>
                            <p class="text-sm text-gray-500">Rayon</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Visualisation de la zone</h4>
                    <div id="map" class="h-96 rounded-lg border border-gray-300"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lat = {{ $geolocationZone->latitude }};
            const lng = {{ $geolocationZone->longitude }};
            const radius = {{ $geolocationZone->radius }};

            // Initialiser la carte
            const map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Marqueur
            L.marker([lat, lng]).addTo(map)
                .bindPopup('<strong>{{ $geolocationZone->name }}</strong><br>Rayon: ' + radius + ' m')
                .openPopup();

            // Cercle de la zone
            L.circle([lat, lng], {
                radius: radius,
                color: '#3B82F6',
                fillColor: '#3B82F6',
                fillOpacity: 0.2
            }).addTo(map);

            // Ajuster le zoom pour voir tout le cercle
            const bounds = L.latLng(lat, lng).toBounds(radius * 2);
            map.fitBounds(bounds);
        });
    </script>
</x-layouts.admin>
