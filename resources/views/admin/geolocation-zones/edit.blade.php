<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.geolocation-zones.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifier : {{ $geolocationZone->name }}
            </h2>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div >
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg animate-fade-in-up">
                <div>
                    <form action="{{ route('admin.geolocation-zones.update', $geolocationZone) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Champs cachés pour les coordonnées -->
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $geolocationZone->latitude) }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $geolocationZone->longitude) }}">

                        <div class="grid gap-6 lg:grid-cols-5">
                            <!-- Colonne gauche : Formulaire (2/5) -->
                            <div class="lg:col-span-2 space-y-5">
                                <!-- Nom -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nom de la zone *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $geolocationZone->name) }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Ex: Siège social, Agence Paris...">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea name="description" id="description" rows="2"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Description optionnelle de cette zone...">{{ old('description', $geolocationZone->description) }}</textarea>
                                </div>

                                <!-- Recherche d'adresse -->
                                <div>
                                    <label for="addressSearch" class="block text-sm font-medium text-gray-700">
                                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Rechercher une adresse
                                    </label>
                                    <div class="mt-1 flex gap-2">
                                        <input type="text" id="addressSearch"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="Ex: 15 Rue de la Paix, Paris">
                                        <button type="button" id="searchBtn"
                                                class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div id="searchResults" class="mt-1 hidden bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto z-50 relative"></div>
                                </div>

                                <!-- Adresse (remplie automatiquement) -->
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700">Adresse sélectionnée</label>
                                    <input type="text" name="address" id="address" value="{{ old('address', $geolocationZone->address) }}" readonly
                                           class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-gray-600 shadow-sm"
                                           placeholder="Cliquez sur la carte ou recherchez une adresse">
                                </div>

                                <!-- Bouton Ma position -->
                                <div>
                                    <button type="button" id="myLocationBtn"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-green-500 text-green-700 rounded-md hover:bg-green-50 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span id="myLocationText">Utiliser ma position actuelle</span>
                                    </button>
                                </div>

                                <!-- Rayon avec slider -->
                                <div>
                                    <label for="radius" class="block text-sm font-medium text-gray-700">
                                        Rayon de la zone : <span id="radiusValue" class="font-bold text-blue-600">{{ $geolocationZone->radius }}</span> mètres
                                    </label>
                                    <input type="range" name="radius" id="radius" value="{{ old('radius', $geolocationZone->radius) }}"
                                           min="10" max="1000" step="10"
                                           class="mt-2 w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                        <span>10m</span>
                                        <span>500m</span>
                                        <span>1km</span>
                                    </div>
                                    @error('radius')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Coordonnées (lecture seule, affichage discret) -->
                                <div class="p-3 bg-gray-50 rounded-md">
                                    <p class="text-xs text-gray-500 mb-1">Coordonnées GPS :</p>
                                    <p class="text-sm font-mono text-gray-600">
                                        <span id="coordsDisplay">{{ $geolocationZone->latitude }}, {{ $geolocationZone->longitude }}</span>
                                    </p>
                                </div>

                                <!-- Options -->
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" id="is_active" value="1"
                                               {{ old('is_active', $geolocationZone->is_active) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                            Zone active
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_default" id="is_default" value="1"
                                               {{ old('is_default', $geolocationZone->is_default) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="is_default" class="ml-2 block text-sm text-gray-900">
                                            Zone par défaut
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne droite : Carte (3/5) -->
                            <div class="lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    📍 Cliquez sur la carte pour modifier la position
                                </label>
                                <div id="map" class="rounded-lg border-2 border-gray-300 shadow-inner" style="height: 500px;"></div>
                                <p class="mt-2 text-sm text-gray-500 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    Le cercle bleu représente la zone dans laquelle les employés pourront pointer.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3 border-t pt-6">
                            <a href="{{ route('admin.geolocation-zones.index') }}"
                               class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Annuler
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const radiusInput = document.getElementById('radius');
            const radiusValue = document.getElementById('radiusValue');
            const addressInput = document.getElementById('address');
            const addressSearch = document.getElementById('addressSearch');
            const searchBtn = document.getElementById('searchBtn');
            const searchResults = document.getElementById('searchResults');
            const coordsDisplay = document.getElementById('coordsDisplay');
            const myLocationBtn = document.getElementById('myLocationBtn');
            const myLocationText = document.getElementById('myLocationText');

            const initialLat = parseFloat(latInput.value);
            const initialLng = parseFloat(lngInput.value);
            const initialRadius = parseInt(radiusInput.value) || 100;

            // Initialiser la carte
            const map = L.map('map').setView([initialLat, initialLng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Marqueur et cercle
            let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);
            let circle = L.circle([initialLat, initialLng], {
                radius: initialRadius,
                color: '#3B82F6',
                fillColor: '#3B82F6',
                fillOpacity: 0.15,
                weight: 2
            }).addTo(map);

            // Mise à jour de la position
            function updatePosition(lat, lng, doReverseGeocode = true) {
                latInput.value = lat.toFixed(8);
                lngInput.value = lng.toFixed(8);
                marker.setLatLng([lat, lng]);
                circle.setLatLng([lat, lng]);
                coordsDisplay.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

                if (doReverseGeocode) {
                    reverseGeocode(lat, lng);
                }
            }

            // Reverse geocoding (coordonnées → adresse)
            async function reverseGeocode(lat, lng) {
                try {
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`,
                        { headers: { 'Accept-Language': 'fr' } }
                    );
                    const data = await response.json();
                    if (data.display_name) {
                        addressInput.value = data.display_name;
                    }
                } catch (error) {
                    console.error('Erreur reverse geocoding:', error);
                }
            }

            // Recherche d'adresse (adresse → coordonnées)
            async function searchAddress(query) {
                if (!query.trim()) return;

                searchBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                try {
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`,
                        { headers: { 'Accept-Language': 'fr' } }
                    );
                    const data = await response.json();

                    if (data.length > 0) {
                        searchResults.innerHTML = data.map((item, index) => `
                            <div class="p-3 hover:bg-blue-50 cursor-pointer border-b last:border-b-0 transition"
                                 data-lat="${item.lat}" data-lng="${item.lon}" data-address="${item.display_name}">
                                <p class="text-sm text-gray-800">${item.display_name}</p>
                            </div>
                        `).join('');
                        searchResults.classList.remove('hidden');

                        // Ajouter les événements de clic
                        searchResults.querySelectorAll('div').forEach(div => {
                            div.addEventListener('click', function() {
                                const lat = parseFloat(this.dataset.lat);
                                const lng = parseFloat(this.dataset.lng);
                                const address = this.dataset.address;

                                updatePosition(lat, lng, false);
                                addressInput.value = address;
                                map.setView([lat, lng], 16);
                                searchResults.classList.add('hidden');
                                addressSearch.value = '';
                            });
                        });
                    } else {
                        searchResults.innerHTML = '<div class="p-3 text-gray-500 text-sm">Aucun résultat trouvé</div>';
                        searchResults.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Erreur recherche:', error);
                    searchResults.innerHTML = '<div class="p-3 text-red-500 text-sm">Erreur lors de la recherche</div>';
                    searchResults.classList.remove('hidden');
                }

                searchBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>';
            }

            // Événements
            searchBtn.addEventListener('click', () => searchAddress(addressSearch.value));
            addressSearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchAddress(addressSearch.value);
                }
            });

            // Fermer les résultats si clic ailleurs
            document.addEventListener('click', (e) => {
                if (!searchResults.contains(e.target) && e.target !== addressSearch && e.target !== searchBtn) {
                    searchResults.classList.add('hidden');
                }
            });

            // Ma position GPS
            myLocationBtn.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('La géolocalisation n\'est pas supportée par votre navigateur');
                    return;
                }

                myLocationText.textContent = 'Localisation en cours...';
                myLocationBtn.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        updatePosition(lat, lng);
                        map.setView([lat, lng], 17);
                        myLocationText.textContent = 'Position trouvée !';
                        myLocationBtn.classList.remove('border-green-500', 'text-green-700', 'hover:bg-green-50');
                        myLocationBtn.classList.add('border-green-600', 'bg-green-50', 'text-green-800');

                        setTimeout(() => {
                            myLocationText.textContent = 'Utiliser ma position actuelle';
                            myLocationBtn.classList.add('border-green-500', 'text-green-700', 'hover:bg-green-50');
                            myLocationBtn.classList.remove('border-green-600', 'bg-green-50', 'text-green-800');
                            myLocationBtn.disabled = false;
                        }, 2000);
                    },
                    (error) => {
                        let message = 'Impossible d\'obtenir votre position';
                        if (error.code === 1) message = 'Accès à la position refusé';
                        else if (error.code === 2) message = 'Position non disponible';
                        else if (error.code === 3) message = 'Délai d\'attente dépassé';

                        myLocationText.textContent = message;
                        myLocationBtn.classList.remove('border-green-500', 'text-green-700');
                        myLocationBtn.classList.add('border-red-500', 'text-red-700');

                        setTimeout(() => {
                            myLocationText.textContent = 'Utiliser ma position actuelle';
                            myLocationBtn.classList.add('border-green-500', 'text-green-700');
                            myLocationBtn.classList.remove('border-red-500', 'text-red-700');
                            myLocationBtn.disabled = false;
                        }, 3000);
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            });

            // Clic sur la carte
            map.on('click', function(e) {
                updatePosition(e.latlng.lat, e.latlng.lng);
            });

            // Drag du marqueur
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updatePosition(pos.lat, pos.lng);
            });

            // Mise à jour du rayon
            radiusInput.addEventListener('input', function() {
                const radius = parseInt(this.value) || 100;
                radiusValue.textContent = radius;
                circle.setRadius(radius);
            });

            // Initialiser l'affichage du rayon
            radiusValue.textContent = initialRadius;
        });
    </script>

    <style>
        /* Style personnalisé pour le slider */
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #3B82F6;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #3B82F6;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
    </style>
</x-layouts.admin>
