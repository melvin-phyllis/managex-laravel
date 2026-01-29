<x-layouts.admin>
    <div class="space-y-6" x-data="employeeForm()">
        <!-- Breadcrumbs -->
        <nav class="flex" aria-label="Breadcrumb">
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
                        <a href="{{ route('admin.employees.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Employés</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Ajouter</span>
                    </div>
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Ajouter un employé</h1>
                <p class="text-gray-500 mt-1">Créer un nouveau compte employé</p>
            </div>
            <a href="{{ route('admin.employees.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start space-x-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium">Génération automatique du mot de passe</p>
                <p class="mt-1">Un mot de passe sécurisé sera généré automatiquement et envoyé par email à l'employé avec ses identifiants de connexion.</p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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

                        <!-- Téléphone -->
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('telephone') border-red-500 @enderror"
                                   placeholder="+33 6 12 34 56 78">
                            @error('telephone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date de naissance -->
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('date_of_birth') border-red-500 @enderror">
                            @error('date_of_birth')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Genre -->
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
                            <select name="gender" id="gender" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Sélectionner</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Homme</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Femme</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Avatar -->
                        <div>
                            <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">Photo de profil</label>
                            <input type="file" name="avatar" id="avatar" accept="image/*"
                                   class="w-full rounded-lg border border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('avatar') border-red-500 @enderror">
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Adresse -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Adresse
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Adresse -->
                        <div class="lg:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="123 rue de la Paix">
                        </div>

                        <!-- Ville -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Paris">
                        </div>

                        <!-- Code postal -->
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="75001">
                        </div>

                        <!-- Pays -->
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                            <input type="text" name="country" id="country" value="{{ old('country', 'France') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Informations professionnelles -->
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
                        <!-- Matricule -->
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                            <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Généré automatiquement si vide">
                            <p class="mt-1 text-xs text-gray-500">Laissez vide pour génération automatique</p>
                        </div>

                        <!-- Département -->
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                            <select name="department_id" id="department_id" x-model="departmentId" @change="loadPositions()"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Sélectionner un département</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position_id" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <select name="position_id" id="position_id" x-model="positionId" :disabled="!departmentId"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100">
                                <option value="">Sélectionner une position</option>
                                <template x-for="position in positions" :key="position.id">
                                    <option :value="position.id" x-text="position.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Poste (libre) -->
                        <div>
                            <label for="poste" class="block text-sm font-medium text-gray-700 mb-1">Intitulé du poste</label>
                            <input type="text" name="poste" id="poste" value="{{ old('poste') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Développeur Full Stack">
                        </div>

                        <!-- Date d'embauche -->
                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">Date d'embauche</label>
                            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Type de contrat -->
                        <div>
                            <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-1">Type de contrat</label>
                            <select name="contract_type" id="contract_type" x-model="contractType"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="cdi" {{ old('contract_type', 'cdi') == 'cdi' ? 'selected' : '' }}>CDI</option>
                                <option value="cdd" {{ old('contract_type') == 'cdd' ? 'selected' : '' }}>CDD</option>
                                <option value="stage" {{ old('contract_type') == 'stage' ? 'selected' : '' }}>Stage</option>
                                <option value="alternance" {{ old('contract_type') == 'alternance' ? 'selected' : '' }}>Alternance</option>
                                <option value="freelance" {{ old('contract_type') == 'freelance' ? 'selected' : '' }}>Freelance</option>
                                <option value="interim" {{ old('contract_type') == 'interim' ? 'selected' : '' }}>Intérim</option>
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
                            <label for="base_salary" class="block text-sm font-medium text-gray-700 mb-1">Salaire brut mensuel (€)</label>
                            <input type="number" name="base_salary" id="base_salary" value="{{ old('base_salary') }}" step="0.01" min="0"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="2500.00">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Jours de travail -->
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
                    <p class="text-sm text-gray-500 mb-4">Sélectionnez les jours où l'employé doit travailler.</p>
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

            <!-- Section 5: Contact d'urgence -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Contact d'urgence
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Nom du contact -->
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du contact</label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Marie Dupont">
                        </div>

                        <!-- Téléphone du contact -->
                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="+33 6 12 34 56 78">
                        </div>

                        <!-- Lien de parenté -->
                        <div>
                            <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-1">Lien de parenté</label>
                            <select name="emergency_contact_relationship" id="emergency_contact_relationship"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Sélectionner</option>
                                <option value="Conjoint(e)" {{ old('emergency_contact_relationship') == 'Conjoint(e)' ? 'selected' : '' }}>Conjoint(e)</option>
                                <option value="Parent" {{ old('emergency_contact_relationship') == 'Parent' ? 'selected' : '' }}>Parent</option>
                                <option value="Enfant" {{ old('emergency_contact_relationship') == 'Enfant' ? 'selected' : '' }}>Enfant</option>
                                <option value="Frère/Sœur" {{ old('emergency_contact_relationship') == 'Frère/Sœur' ? 'selected' : '' }}>Frère/Sœur</option>
                                <option value="Ami(e)" {{ old('emergency_contact_relationship') == 'Ami(e)' ? 'selected' : '' }}>Ami(e)</option>
                                <option value="Autre" {{ old('emergency_contact_relationship') == 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 6: Informations administratives -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Informations administratives
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Numéro de sécurité sociale -->
                        <div>
                            <label for="social_security_number" class="block text-sm font-medium text-gray-700 mb-1">N° Sécurité sociale</label>
                            <input type="text" name="social_security_number" id="social_security_number" value="{{ old('social_security_number') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="1 85 12 75 115 001 42">
                        </div>

                        <!-- IBAN -->
                        <div>
                            <label for="bank_iban" class="block text-sm font-medium text-gray-700 mb-1">IBAN</label>
                            <input type="text" name="bank_iban" id="bank_iban" value="{{ old('bank_iban') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="FR76 3000 1007 1600 0000 0000 043">
                        </div>

                        <!-- BIC -->
                        <div>
                            <label for="bank_bic" class="block text-sm font-medium text-gray-700 mb-1">BIC</label>
                            <input type="text" name="bank_bic" id="bank_bic" value="{{ old('bank_bic') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="BNPAFRPP">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-4 py-4">
                <a href="{{ route('admin.employees.index') }}" class="px-6 py-2.5 text-gray-700 hover:text-gray-900 font-medium">Annuler</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Créer l'employé
                </button>
            </div>
        </form>
    </div>

    <script>
        function employeeForm() {
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
                        const response = await fetch(`/admin/departments/${this.departmentId}/positions`);
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
