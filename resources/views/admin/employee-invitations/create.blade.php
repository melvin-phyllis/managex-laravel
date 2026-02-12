<x-layouts.admin>
    <div class="space-y-6" x-data="invitationForm()">
        <!-- Breadcrumbs -->
        <nav class="flex animate-fade-in-up" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <a href="{{ route('admin.employees.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Employes</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Inviter par email</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex items-center justify-between animate-fade-in-up animation-delay-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Inviter un employe</h1>
                <p class="text-gray-500 mt-1">Envoyez un lien par email pour que l'employe complete son profil</p>
            </div>
            <a href="{{ route('admin.employees.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start space-x-3 animate-fade-in-up animation-delay-200">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium">Comment ca fonctionne ?</p>
                <p class="mt-1">Un email sera envoye a l'employe avec un lien securise. Il pourra completer ses informations personnelles (telephone, adresse, contact d'urgence) et choisir son mot de passe. Le lien expire apres 48 heures.</p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.employee-invitations.store') }}" method="POST" class="space-y-6 animate-fade-in-up animation-delay-300">
            @csrf

            <!-- Section 1: Informations de base -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informations de base
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                   placeholder="Jean Dupont">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                                   placeholder="jean.dupont@example.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Informations professionnelles -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Informations professionnelles
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Departement -->
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Departement <span class="text-red-500">*</span></label>
                            <select name="department_id" id="department_id" required x-model="departmentId" @change="loadPositions()"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selectionner un departement</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position_id" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <select name="position_id" id="position_id" x-model="positionId" :disabled="!departmentId"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100">
                                <option value="">Selectionner une position</option>
                                <template x-for="position in positions" :key="position.id">
                                    <option :value="position.id" x-text="position.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Poste -->
                        <div>
                            <label for="poste" class="block text-sm font-medium text-gray-700 mb-1">Intitule du poste <span class="text-red-500">*</span></label>
                            <input type="text" name="poste" id="poste" value="{{ old('poste') }}" required
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('poste') border-red-500 @enderror"
                                   placeholder="Developpeur Full Stack">
                            @error('poste')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date d'embauche -->
                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">Date d'embauche <span class="text-red-500">*</span></label>
                            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('hire_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type de contrat -->
                        <div>
                            <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-1">Type de contrat <span class="text-red-500">*</span></label>
                            <select name="contract_type" id="contract_type" required x-model="contractType"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="cdi" {{ old('contract_type', 'cdi') == 'cdi' ? 'selected' : '' }}>CDI</option>
                                <option value="cdd" {{ old('contract_type') == 'cdd' ? 'selected' : '' }}>CDD</option>
                                <option value="stage" {{ old('contract_type') == 'stage' ? 'selected' : '' }}>Stage</option>
                                <option value="alternance" {{ old('contract_type') == 'alternance' ? 'selected' : '' }}>Alternance</option>
                                <option value="freelance" {{ old('contract_type') == 'freelance' ? 'selected' : '' }}>Freelance</option>
                                <option value="interim" {{ old('contract_type') == 'interim' ? 'selected' : '' }}>Interim</option>
                            </select>
                        </div>

                        <!-- Date de fin de contrat -->
                        <div x-show="['cdd', 'stage', 'alternance', 'interim'].includes(contractType)" x-transition>
                            <label for="contract_end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin de contrat</label>
                            <input type="date" name="contract_end_date" id="contract_end_date" value="{{ old('contract_end_date') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Salaire de base -->
                        <div>
                            <label for="base_salary" class="block text-sm font-medium text-gray-700 mb-1">Salaire brut mensuel (FCFA)</label>
                            <input type="number" name="base_salary" id="base_salary" value="{{ old('base_salary') }}" step="0.01" min="0"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="250000">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Jours de travail -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Jours de travail <span class="text-red-500 ml-1">*</span>
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-4">Selectionnez les jours de travail de l'employe.</p>
                    <div class="flex flex-wrap gap-4">
                        @php
                            $days = [
                                1 => 'Lundi',
                                2 => 'Mardi',
                                3 => 'Mercredi',
                                4 => 'Jeudi',
                                5 => 'Vendredi',
                                6 => 'Samedi',
                                7 => 'Dimanche',
                            ];
                            $defaultWorkDays = old('work_days', [1, 2, 3, 4, 5]);
                        @endphp
                        @foreach($days as $value => $label)
                            <label class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300">
                                <input type="checkbox" name="work_days[]" value="{{ $value }}"
                                       {{ in_array($value, $defaultWorkDays) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('work_days')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-4 py-4">
                <a href="{{ route('admin.employees.index') }}" class="px-6 py-2.5 text-gray-700 hover:text-gray-900 font-medium">Annuler</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Envoyer l'invitation
                </button>
            </div>
        </form>
    </div>

    <script nonce="{{ $cspNonce ?? '' }}">
        function invitationForm() {
            return {
                departmentId: '{{ old('department_id', '') }}',
                positionId: '{{ old('position_id', '') }}',
                contractType: '{{ old('contract_type', 'cdi') }}',
                positions: [],

                init() {
                    if (this.departmentId) {
                        this.loadPositions();
                    }
                },

                async loadPositions() {
                    if (!this.departmentId) {
                        this.positions = [];
                        this.positionId = '';
                        return;
                    }

                    try {
                        const response = await fetch(`{{ url('/admin/departments') }}/${this.departmentId}/positions`);
                        this.positions = await response.json();
                    } catch (error) {
                        console.error('Erreur lors du chargement des positions:', error);
                        this.positions = [];
                    }
                }
            }
        }
    </script>
</x-layouts.admin>
