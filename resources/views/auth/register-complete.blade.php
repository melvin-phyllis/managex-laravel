<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Complétez votre profil pour finaliser la création de votre compte.') }}
    </div>

    <form method="POST" action="{{ route('register.complete.post') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nom complet')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full disabled:bg-gray-100" type="email" name="email"
                :value="old('email', $regCode->email)" :readonly="$regCode->email ? true : false" required />
            @if($regCode->email)
                <p class="text-xs text-gray-500 mt-1">L'adresse email est liée à ce code d'invitation.</p>
            @endif
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Personal Info (Phone, Address, Birth Date, Gender) -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Téléphone')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="address" :value="__('Adresse')" />
            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="department_id" :value="__('Département')" />
                <select id="department_id" name="department_id" required
                    class="block mt-1 w-full border-gray-300 focus:border-[#3D7A6A] focus:ring-[#3D7A6A] rounded-md shadow-sm">
                    <option value="">Choisir</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $regCode->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="position_id" :value="__('Poste')" />
                <select id="position_id" name="position_id" required
                    class="block mt-1 w-full border-gray-300 focus:border-[#3D7A6A] focus:ring-[#3D7A6A] rounded-md shadow-sm">
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

        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="birth_date" :value="__('Date de naissance')" />
                <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date"
                    :value="old('birth_date')" />
                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="gender" :value="__('Genre')" />
                <select id="gender" name="gender"
                    class="block mt-1 w-full border-gray-300 focus:border-[#3D7A6A] focus:ring-[#3D7A6A] rounded-md shadow-sm">
                    <option value="">Sélectionnez</option>
                    <option value="male">Homme</option>
                    <option value="female">Femme</option>
                </select>
                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
            </div>
        </div>

        <!-- Role info (Read-only for info) -->
        <div class="mt-6 p-3 bg-gray-100 rounded text-xs text-gray-500">
            <strong>Information d'organisation :</strong><br>
            Rôle : {{ $regCode->role === 'admin' ? 'Administrateur' : 'Employé' }}
        </div>

        <hr class="my-6">

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="ms-4">
                {{ __('Finaliser mon compte') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
