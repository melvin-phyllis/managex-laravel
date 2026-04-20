<x-guest-layout>
    <div class="form-card anim-1">
        <h2 class="form-title">Finalisation du profil</h2>
        <p class="form-subtitle">Complétez ces dernières informations pour activer votre compte ManageX.</p>

        <form method="POST" action="{{ route('register.complete.post') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <!-- Name -->
            <div class="field anim-2">
                <x-input-label for="name" :value="__('Nom complet')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                    autofocus placeholder="ex: Jean Dupont" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="field anim-2 mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full bg-gray-50 opacity-75 cursor-not-allowed" type="email" name="email"
                    :value="old('email', $regCode->email)" :readonly="$regCode->email ? true : false" required />
                @if($regCode->email)
                    <p class="text-[10px] text-gray-500 mt-1 italic">Note: Cette adresse est liée à votre code d'invitation.</p>
                @endif
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 anim-3">
                <!-- Phone -->
                <div class="field">
                    <x-input-label for="phone" :value="__('Téléphone')" />
                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="+225 ..." />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <!-- Address -->
                <div class="field">
                    <x-input-label for="address" :value="__('Adresse')" />
                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" placeholder="Quartier, Ville" />
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 anim-3">
                <!-- Department -->
                <div class="field">
                    <x-input-label for="department_id" :value="__('Département')" />
                    <select id="department_id" name="department_id" required
                        class="block mt-1 w-full border-gray-300 focus:border-[#C8A96E] focus:ring-[#C8A96E] rounded-xl shadow-sm bg-gray-50 text-sm">
                        <option value="">Choisir</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $regCode->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                </div>

                <!-- Position -->
                <div class="field">
                    <x-input-label for="position_id" :value="__('Poste')" />
                    <select id="position_id" name="position_id" required
                        class="block mt-1 w-full border-gray-300 focus:border-[#C8A96E] focus:ring-[#C8A96E] rounded-xl shadow-sm bg-gray-50 text-sm">
                        <option value="">Choisir</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id', $regCode->position_id) == $pos->id ? 'selected' : '' }}>
                                {{ $pos->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('position_id')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 anim-3">
                <!-- Birth Date -->
                <div class="field">
                    <x-input-label for="birth_date" :value="__('Date de naissance')" />
                    <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date"
                        :value="old('birth_date')" />
                    <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                </div>

                <!-- Gender -->
                <div class="field">
                    <x-input-label for="gender" :value="__('Genre')" />
                    <select id="gender" name="gender"
                        class="block mt-1 w-full border-gray-300 focus:border-[#C8A96E] focus:ring-[#C8A96E] rounded-xl shadow-sm bg-gray-50 text-sm">
                        <option value="">Sélectionnez</option>
                        <option value="male">Homme</option>
                        <option value="female">Femme</option>
                    </select>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                </div>
            </div>

            <!-- Role info (Read-only for info) -->
            <div class="mt-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-xs text-emerald-800 anim-4">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <strong class="uppercase letter-spacing-wider">Information d'organisation</strong>
                </div>
                Rôle assigné : <span class="font-bold">{{ $regCode->role === 'admin' ? 'Administrateur' : 'Employé' }}</span>
            </div>

            <div class="divider my-8 anim-4"><span>Sécurité</span></div>

            <!-- Password -->
            <div class="field anim-4">
                <x-input-label for="password" :value="__('Nouveau mot de passe')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="field anim-4 mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="mt-8 mb-4 anim-4">
                <button type="submit" class="btn-submit" :disabled="loading">
                    <template x-if="!loading">
                        <span class="flex items-center gap-2">
                            {{ __('Finaliser la création de mon compte') }}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                    </template>
                    <template x-if="loading">
                        <span class="flex items-center gap-2">
                            <span class="spinner !border-white/30 !border-top-white"></span>
                            Traitement...
                        </span>
                    </template>
                </button>
            </div>
        </form>
    </div>

    <p class="login-footer anim-4">
        Besoin d'aide ? <a href="mailto:support@managex.ci">Contacter le support</a>
    </p>
</x-guest-layout>
