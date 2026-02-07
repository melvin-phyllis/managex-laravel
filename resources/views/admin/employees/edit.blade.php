<x-layouts.admin>
    <div class="space-y-6" x-data="employeeEditForm()">
        <!-- Header avec gradient -->
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
                                <li><a href="{{ route('admin.employees.index') }}" class="text-white/70 hover:text-white text-sm">Employés</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Modifier</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            Modifier l'employé
                        </h1>
                        <p class="text-white/80 mt-2 flex items-center gap-2">
                            {{ $employee->name }}
                            <span class="px-2 py-0.5 bg-white/20 backdrop-blur-sm text-white text-xs font-medium rounded-full">
                                {{ $employee->employee_id ?? 'N/A' }}
                            </span>
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.employees.show', $employee) }}" 
                           class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Voir le profil
                        </a>
                        <a href="{{ route('admin.employees.index') }}" 
                           class="px-4 py-2.5 bg-white/20 backdrop-blur-sm text-white font-semibold rounded-xl hover:bg-white/30 transition-all flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data" class="space-y-6 animate-fade-in-up animation-delay-100">
            @csrf
            @method('PUT')

            <!-- Section 1: Informations de base -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informations de base
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Avatar avec preview -->
                        <div class="lg:col-span-3 flex items-center space-x-6 pb-4 border-b border-gray-100">
                            @if($employee->avatar)
                                <img src="{{ avatar_url($employee->avatar) }}" alt="{{ $employee->name }}" class="w-20 h-20 rounded-xl object-cover border-4 border-white shadow-lg">
                            @else
                                <div class="w-20 h-20 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                    <span class="text-white font-bold text-2xl">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                                </div>
                            @endif
                            <div class="flex-1">
                                <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">Changer la photo de profil</label>
                                <input type="file" name="avatar" id="avatar" accept="image/*"
                                       class="w-full rounded-xl border border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:text-white hover:file:opacity-90" style="--tw-file-bg: #5680E9;">
                                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Taille max: 2 Mo</p>
                            </div>
                        </div>

                        <!-- Nom -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $employee->name) }}" required
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" required
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Téléphone -->
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $employee->telephone) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Date de naissance -->
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Genre -->
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
                            <select name="gender" id="gender" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sélectionner</option>
                                <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Homme</option>
                                <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Femme</option>
                                <option value="other" {{ old('gender', $employee->gender) == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        <!-- Statut -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select name="status" id="status" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="on_leave" {{ old('status', $employee->status) == 'on_leave' ? 'selected' : '' }}>En congé</option>
                                <option value="suspended" {{ old('status', $employee->status) == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                <option value="terminated" {{ old('status', $employee->status) == 'terminated' ? 'selected' : '' }}>Parti</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Mot de passe -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #F59E0B, #FBBF24);">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Sécurité
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                            <input type="password" name="password" id="password"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Laisser vide pour conserver le mot de passe actuel</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Adresse -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #5AB9EA, #84CEEB);">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Adresse
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="lg:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <input type="text" name="address" id="address" value="{{ old('address', $employee->address) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $employee->city) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $employee->postal_code) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                            <select name="country" id="country" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @include('partials.country-options', ['selected' => old('country', $employee->country ?? 'CI')])
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Informations professionnelles -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #8860D0, #C1C8E4);">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Informations professionnelles
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                            <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id', $employee->employee_id) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                            <select name="department_id" id="department_id" x-model="departmentId" @change="loadPositions()"
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sélectionner un département</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="position_id" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <select name="position_id" id="position_id" x-model="positionId" :disabled="!departmentId"
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100">
                                <option value="">Sélectionner une position</option>
                                <template x-for="position in positions" :key="position.id">
                                    <option :value="position.id" x-text="position.name" :selected="position.id == positionId"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label for="poste" class="block text-sm font-medium text-gray-700 mb-1">Intitulé du poste</label>
                            <input type="text" name="poste" id="poste" value="{{ old('poste', $employee->poste) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">Date d'embauche</label>
                            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-1">Type de contrat</label>
                            <select name="contract_type" id="contract_type" x-model="contractType"
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="cdi" {{ old('contract_type', $employee->contract_type) == 'cdi' ? 'selected' : '' }}>CDI</option>
                                <option value="cdd" {{ old('contract_type', $employee->contract_type) == 'cdd' ? 'selected' : '' }}>CDD</option>
                                <option value="stage" {{ old('contract_type', $employee->contract_type) == 'stage' ? 'selected' : '' }}>Stage</option>
                                <option value="alternance" {{ old('contract_type', $employee->contract_type) == 'alternance' ? 'selected' : '' }}>Alternance</option>
                                <option value="freelance" {{ old('contract_type', $employee->contract_type) == 'freelance' ? 'selected' : '' }}>Freelance</option>
                                <option value="interim" {{ old('contract_type', $employee->contract_type) == 'interim' ? 'selected' : '' }}>Intérim</option>
                            </select>
                        </div>

                        <div x-show="['cdd', 'stage', 'alternance', 'interim'].includes(contractType)" x-transition>
                            <label for="contract_end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin de contrat</label>
                            <input type="date" name="contract_end_date" id="contract_end_date" value="{{ old('contract_end_date', $employee->contract_end_date?->format('Y-m-d')) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="base_salary" class="block text-sm font-medium text-gray-700 mb-1">Salaire brut mensuel (FCFA)</label>
                            <input type="number" name="base_salary" id="base_salary" value="{{ old('base_salary', $employee->base_salary) }}" step="0.01" min="0"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Fiscalité (CIV) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #5AB9EA, #5680E9);">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Fiscalité (Côte d'Ivoire)
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-1">Situation familiale</label>
                            <select name="marital_status" id="marital_status" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Célibataire</option>
                                <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>Marié(e)</option>
                                <option value="divorced" {{ old('marital_status', $employee->marital_status) == 'divorced' ? 'selected' : '' }}>Divorcé(e)</option>
                                <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed' ? 'selected' : '' }}>Veuf/Veuve</option>
                            </select>
                        </div>

                        <div>
                            <label for="children_count" class="block text-sm font-medium text-gray-700 mb-1">Nombre d'enfants</label>
                            <input type="number" name="children_count" id="children_count" value="{{ old('children_count', $employee->children_count ?? 0) }}" min="0"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="cnps_number" class="block text-sm font-medium text-gray-700 mb-1">Numéro CNPS</label>
                            <input type="text" name="cnps_number" id="cnps_number" value="{{ old('cnps_number', $employee->cnps_number) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="number_of_parts" class="block text-sm font-medium text-gray-700 mb-1">Parts fiscales (Override)</label>
                            <input type="number" name="number_of_parts" id="number_of_parts" value="{{ old('number_of_parts', $employee->number_of_parts) }}" step="0.5" min="1"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Auto par défaut">
                            <p class="mt-1 text-xs text-gray-500">Laisser vide pour calcul auto</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 6: Jours de travail -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #F59E0B, #FCD34D);">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Jours de travail <span class="text-white/80 ml-1">*</span>
                    </h2>
                </div>
                <div class="p-6">
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
                            $employeeWorkDays = $employee->workDays->pluck('day_of_week')->toArray();
                        @endphp
                        @foreach($days as $value => $label)
                            <label class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 hover:bg-purple-50 cursor-pointer transition-colors has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-300">
                                <input type="checkbox" name="work_days[]" value="{{ $value }}"
                                       {{ in_array($value, old('work_days', $employeeWorkDays)) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('work_days')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section 7: Contact d'urgence -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #EF4444, #F87171);">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Contact d'urgence
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du contact</label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-1">Lien de parenté</label>
                            <select name="emergency_contact_relationship" id="emergency_contact_relationship"
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sélectionner</option>
                                <option value="Conjoint(e)" {{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) == 'Conjoint(e)' ? 'selected' : '' }}>Conjoint(e)</option>
                                <option value="Parent" {{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) == 'Parent' ? 'selected' : '' }}>Parent</option>
                                <option value="Enfant" {{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) == 'Enfant' ? 'selected' : '' }}>Enfant</option>
                                <option value="Frère/Sœur" {{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) == 'Frère/Sœur' ? 'selected' : '' }}>Frère/Sœur</option>
                                <option value="Ami(e)" {{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) == 'Ami(e)' ? 'selected' : '' }}>Ami(e)</option>
                                <option value="Autre" {{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) == 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 8: Informations administratives -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #5680E9, #8860D0);">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Informations administratives
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="social_security_number" class="block text-sm font-medium text-gray-700 mb-1">N° Sécurité sociale</label>
                            <input type="text" name="social_security_number" id="social_security_number" value="{{ old('social_security_number', $employee->social_security_number) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="bank_iban" class="block text-sm font-medium text-gray-700 mb-1">IBAN</label>
                            <input type="text" name="bank_iban" id="bank_iban" value="{{ old('bank_iban', $employee->bank_iban) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="bank_bic" class="block text-sm font-medium text-gray-700 mb-1">BIC</label>
                            <input type="text" name="bank_bic" id="bank_bic" value="{{ old('bank_bic', $employee->bank_bic) }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 9: Soldes de congés -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="leave_balance" class="block text-sm font-medium text-gray-700 mb-1">Congés payés (jours)</label>
                            <input type="number" name="leave_balance" id="leave_balance" value="{{ old('leave_balance', $employee->leave_balance ?? 25) }}" step="0.5" min="0"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="rtt_balance" class="block text-sm font-medium text-gray-700 mb-1">RTT (jours)</label>
                            <input type="number" name="rtt_balance" id="rtt_balance" value="{{ old('rtt_balance', $employee->rtt_balance ?? 0) }}" step="0.5" min="0"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="sick_leave_balance" class="block text-sm font-medium text-gray-700 mb-1">Congés maladie (jours)</label>
                            <input type="number" name="sick_leave_balance" id="sick_leave_balance" value="{{ old('sick_leave_balance', $employee->sick_leave_balance ?? 0) }}" step="0.5" min="0"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" readonly>
                            <p class="mt-1 text-xs text-gray-500">Calculé automatiquement</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 10: Notes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #6B7280, #9CA3AF);">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Notes internes
                    </h2>
                </div>
                <div class="p-6">
                    <textarea name="notes" id="notes" rows="4"
                              class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Notes internes sur l'employé (non visible par l'employé)...">{{ old('notes', $employee->notes) }}</textarea>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-4 py-4">
                <a href="{{ route('admin.employees.index') }}" class="px-6 py-2.5 text-gray-700 hover:text-gray-900 font-medium">Annuler</a>
                <button type="submit" class="px-6 py-2.5 text-white font-semibold rounded-xl transition-all shadow-lg flex items-center" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <script nonce="{{ $cspNonce ?? '' }}">
        function employeeEditForm() {
            return {
                departmentId: '{{ old('department_id', $employee->department_id ?? '') }}',
                positionId: '{{ old('position_id', $employee->position_id ?? '') }}',
                contractType: '{{ old('contract_type', $employee->contract_type ?? 'cdi') }}',
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
