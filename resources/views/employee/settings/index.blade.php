<x-layouts.employee>
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between animate-fade-in-up">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Paramètres</h1>
                <p class="text-gray-500 mt-1">Gérez vos préférences de compte</p>
            </div>
        </div>

        @if(session('success'))
            <div class="px-4 py-3 rounded-lg flex items-center gap-3" style="background-color: rgba(59, 139, 235, 0.1); border: 1px solid #3B8BEB; color: #3B8BEB;">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="px-4 py-3 rounded-lg flex items-center gap-3" style="background-color: rgba(178, 56, 80, 0.1); border: 1px solid #B23850; color: #B23850;">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 animate-fade-in-up animation-delay-100" x-data="{ activeTab: 'security' }">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'security'"
                        :class="activeTab === 'security' ? 'text-blue-600 border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        :style="activeTab === 'security' ? 'border-color: #3B8BEB; color: #3B8BEB;' : ''"
                        class="whitespace-nowrap py-4 px-1 font-medium text-sm flex items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Sécurité
                </button>
                <button @click="activeTab = 'notifications'"
                        :class="activeTab === 'notifications' ? 'text-blue-600 border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        :style="activeTab === 'notifications' ? 'border-color: #3B8BEB; color: #3B8BEB;' : ''"
                        class="whitespace-nowrap py-4 px-1 font-medium text-sm flex items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Notifications
                </button>
            </nav>

            <!-- Tab: Sécurité -->
            <div x-show="activeTab === 'security'" x-cloak class="py-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center mb-6">
                        <div class="p-3 rounded-full mr-4" style="background-color: rgba(59, 139, 235, 0.1);">
                            <svg class="w-6 h-6" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Modifier le mot de passe</h2>
                            <p class="text-sm text-gray-500">Assurez-vous d'utiliser un mot de passe sécurisé que vous n'utilisez pas ailleurs.</p>
                        </div>
                    </div>

                    <form action="{{ route('employee.settings.password') }}" method="POST" class="max-w-md">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Mot de passe actuel
                                </label>
                                <input type="password" name="current_password" id="current_password" required
                                       class="w-full rounded-lg border-gray-300 focus:ring-opacity-50 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nouveau mot de passe
                                </label>
                                <input type="password" name="password" id="password" required minlength="8"
                                       class="w-full rounded-lg border-gray-300 focus:ring-opacity-50 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                                <p class="text-xs text-gray-500 mt-1">Minimum 8 caractères, avec majuscule, minuscule et chiffre</p>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirmer le nouveau mot de passe
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="w-full rounded-lg border-gray-300 focus:ring-opacity-50 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="px-6 py-2.5 text-white font-medium rounded-lg hover:opacity-90 transition-opacity" style="background-color: #3B8BEB;">
                                Mettre à jour le mot de passe
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Session info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-gray-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Session active</h2>
                            <p class="text-sm text-gray-500">Informations sur votre session actuelle.</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: rgba(59, 139, 235, 0.1);">
                                    <svg class="w-5 h-5" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Cette session</p>
                                    <p class="text-xs text-gray-500">Connecté depuis {{ request()->ip() }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full" style="background-color: rgba(59, 139, 235, 0.1); color: #3B8BEB;">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Notifications -->
            <div x-show="activeTab === 'notifications'" x-cloak class="py-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center mb-6">
                        <div class="p-3 rounded-full mr-4" style="background-color: rgba(59, 139, 235, 0.1);">
                            <svg class="w-6 h-6" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Préférences de notifications</h2>
                            <p class="text-sm text-gray-500">Choisissez comment vous souhaitez être notifié.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">Notifications par email</p>
                                <p class="text-sm text-gray-500">Recevez des emails pour les mises à jour importantes</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">Notifications de messages</p>
                                <p class="text-sm text-gray-500">Soyez notifié des nouveaux messages dans la messagerie</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">Rappels de tâches</p>
                                <p class="text-sm text-gray-500">Recevez des rappels pour vos tâches à venir</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all" style="background-color: #3B8BEB;"></div>
                            </label>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mt-4">Les modifications des préférences de notification seront bientôt disponibles.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.employee>
