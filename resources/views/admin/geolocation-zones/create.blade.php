<x-layouts.admin>
    <x-slot name="header">
        <!-- Header moderne avec gradient -->
        <div class="-mx-4 sm:-mx-6 lg:-mx-8 -mt-4 px-4 sm:px-6 lg:px-8 py-6 mb-4" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl sm:text-2xl font-bold text-white">Nouvelle zone de géolocalisation</h2>
                                    <p class="text-blue-100 text-sm mt-0.5">Définissez une zone géographique pour le pointage</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Info box -->
        <div class="mb-6 border rounded-xl p-4" style="background: linear-gradient(90deg, #5680E920, #84CEEB10); border-color: #5680E940;">
            <div class="flex items-start gap-3">
                <div class="p-2 rounded-lg flex-shrink-0" style="background-color: #5680E920;">
                    <svg class="w-5 h-5" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold" style="color: #5680E9;">Comment créer une zone ?</h3>
                    <p class="text-sm mt-1" style="color: #5680E9CC;">
                        Recherchez une adresse, utilisez votre position GPS, ou cliquez directement sur la carte pour définir le centre de la zone. 
                        Ajustez ensuite le rayon pour délimiter la zone de pointage autorisée.
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.geolocation-zones.store') }}" method="POST" id="zoneForm">
            @csrf

            <!-- Champs cachés pour les coordonnées -->
            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', '48.8566') }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', '2.3522') }}">

            <div class="grid gap-6 lg:grid-cols-5">
                <!-- Colonne gauche : Formulaire (2/5 sur desktop, pleine largeur sur mobile) -->
                <div class="lg:col-span-2 order-2 lg:order-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-white px-5 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Informations de la zone
                            </h3>
                        </div>

                        <div class="p-5 space-y-5">
                            <!-- Nom -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Nom de la zone <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                                       placeholder="Ex: Siége social, Agence Paris...">
                                @error('name')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                                <textarea name="description" id="description" rows="2"
                                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                                          placeholder="Description optionnelle de cette zone...">{{ old('description') }}</textarea>
                            </div>

                            <!-- Recherche d'adresse -->
                            <div>
                                <label for="addressSearch" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <svg class="inline w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Rechercher une adresse
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" id="addressSearch"
                                           class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                                           placeholder="Ex: 15 Rue de la Paix, Paris">
                                    <button type="button" id="searchBtn"
                                            class="px-4 py-2 text-white rounded-lg transition-colors flex items-center gap-2 flex-shrink-0" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        <span class="hidden sm:inline">Rechercher</span>
                                    </button>
                                </div>
                                <div id="searchResults" class="mt-2 hidden bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto z-50 relative"></div>
                            </div>

                            <!-- Adresse sélectionnée -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Adresse sélectionnée</label>
                                <input type="text" name="address" id="address" value="{{ old('address') }}" readonly
                                       class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-600 shadow-sm cursor-not-allowed"
                                       placeholder="Cliquez sur la carte ou recherchez une adresse">
                            </div>

                            <!-- Bouton Ma position -->
                            <button type="button" id="myLocationBtn"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-green-500 text-green-700 rounded-lg hover:bg-green-50 transition-all font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span id="myLocationText">Utiliser ma position actuelle</span>
                            </button>

                            <!-- Rayon avec slider -->
                            <div>
                                <label for="radius" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Rayon de la zone : <span id="radiusValue" class="font-bold" style="color: #5680E9;">100</span> mètres
                                </label>
                                <input type="range" name="radius" id="radius" value="{{ old('radius', 100) }}"
                                       min="10" max="1000" step="10"
                                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>10m</span>
                                    <span>500m</span>
                                    <span>1km</span>
                                </div>
                                @error('radius')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Coordonnées GPS -->
                            <div class="p-3 rounded-lg border border-gray-100" style="background: linear-gradient(90deg, #5680E910, #84CEEB10);">
                                <p class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                    Coordonnées GPS :
                                </p>
                                <p class="text-sm font-mono text-gray-700">
                                    <span id="coordsDisplay">48.8566, 2.3522</span>
                                </p>
                            </div>

                            <!-- Options -->
                            <div class="space-y-3 pt-2">
                                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                           class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <div>
                                        <span class="block text-sm font-medium text-gray-900">Zone active</span>
                                        <span class="text-xs text-gray-500">Les employés pourront pointer dans cette zone</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                                    <input type="checkbox" name="is_default" id="is_default" value="1"
                                           class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <div>
                                        <span class="block text-sm font-medium text-gray-900">Zone par défaut</span>
                                        <span class="text-xs text-gray-500">Cette zone sera proposée en priorité</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action (version mobile) -->
                    <div class="mt-4 flex flex-col sm:flex-row gap-3 lg:hidden">
                        <a href="{{ route('admin.geolocation-zones.index') }}"
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors text-center">
                            Annuler
                        </a>
                        <button type="submit"
                                class="flex-1 px-4 py-3 text-white rounded-lg text-sm font-semibold transition-all shadow-sm flex items-center justify-center gap-2" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Créer la zone
                        </button>
                    </div>
                </div>

                <!-- Colonne droite : Carte (3/5 sur desktop, pleine largeur sur mobile) -->
                <div class="lg:col-span-3 order-1 lg:order-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-white px-5 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                                Carte interactive
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Cliquez pour définir la position ou déplacez le marqueur</p>
                        </div>

                        <div class="p-4">
                            <div id="map" class="rounded-lg border-2 border-gray-200 shadow-inner" style="height: 400px; min-height: 300px;"></div>
                            <div class="mt-3 flex items-start gap-2 text-sm text-gray-600 bg-blue-50 p-3 rounded-lg">
                                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Le cercle bleu représente la zone dans laquelle les employés pourront pointer leur présence.</span>
                            </div>
                        </div>

                        <!-- Boutons d'action (version desktop) -->
                        <div class="hidden lg:flex justify-end gap-3 px-5 py-4 bg-gray-50 border-t border-gray-100">
                            <a href="{{ route('admin.geolocation-zones.index') }}"
                               class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-white transition-colors">
                                Annuler
                            </a>
                            <button type="submit"
                                    class="px-6 py-2.5 text-white rounded-lg text-sm font-semibold transition-all shadow-sm flex items-center gap-2" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Créer la zone
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Leaflet JS -->
    <script nonce="{{ $cspNonce ?? '' }}" src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script nonce="{{ $cspNonce ?? '' }}">
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

            const initialLat = parseFloat(latInput.value) || 48.8566;
            const initialLng = parseFloat(lngInput.value) || 2.3522;
            const initialRadius = parseInt(radiusInput.value) || 100;

            // Initialiser la carte
            const map = L.map('map').setView([initialLat, initialLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'é‚Â OpenStreetMap'
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

            // Séƒâ€°CURITéƒâ€°: Fonction d'échappement HTML pour prévenir les XSS
            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Mise é  jour de la position
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

            // Reverse geocoding (coordonnées aâ€ â€™ adresse)
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

            // Recherche d'adresse (adresse aâ€ â€™ coordonnées)
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
                        // Séƒâ€°CURITéƒâ€°: éƒâ€°chapper les données de l'API externe pour prévenir XSS
                        searchResults.innerHTML = data.map((item, index) => `
                            <div class="p-3 hover:bg-blue-50 cursor-pointer border-b last:border-b-0 transition"
                                 data-lat="${escapeHtml(String(item.lat))}" 
                                 data-lng="${escapeHtml(String(item.lon))}" 
                                 data-address="${escapeHtml(item.display_name)}">
                                <p class="text-sm text-gray-800">${escapeHtml(item.display_name)}</p>
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

                searchBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg><span class="hidden sm:inline ml-2">Rechercher</span>';
            }

            // éƒâ€°vénements
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
                        if (error.code === 1) message = 'Accés é  la position refusé';
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

            // Mise é  jour du rayon
            radiusInput.addEventListener('input', function() {
                const radius = parseInt(this.value) || 100;
                radiusValue.textContent = radius;
                circle.setRadius(radius);
            });

            // Initialiser l'affichage du rayon
            radiusValue.textContent = initialRadius;

            // Resize map when container size changes (for responsive)
            setTimeout(() => map.invalidateSize(), 100);
            window.addEventListener('resize', () => map.invalidateSize());
        });
    </script>

    <style>
        /* Fix z-index pour éviter que la carte passe au-dessus du chatbot */
        #map {
            position: relative;
            z-index: 0;
        }
        .leaflet-pane,
        .leaflet-control {
            z-index: 1 !important;
        }
        .leaflet-top,
        .leaflet-bottom {
            z-index: 2 !important;
        }
        
        /* Style personnalisé pour le slider */
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #5680E9;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #5680E9;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
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
    </style>
</x-layouts.admin>
