<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Entrez voter code de création pour commencer l\'inscription.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('register.verify.post') }}">
        @csrf

        <!-- Registration Code -->
        <div>
            <x-input-label for="registration_code" :value="__('Code d\'inscription')" />
            <x-text-input id="registration_code" class="block mt-1 w-full" type="text" name="registration_code"
                :value="old('registration_code')" required autofocus placeholder="Ex: ABCD-1234" />
            <x-input-error :messages="$errors->get('registration_code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#3D7A6A]"
                href="{{ route('login') }}">
                {{ __('Déjà inscrit ?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Vérifier le code') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
