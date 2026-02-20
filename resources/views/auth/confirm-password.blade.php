<x-guest-layout>
    <div class="form-card">
        <h1 class="form-title">Confirmer le mot de passe</h1>
        <p class="form-subtitle">Ceci est une zone sécurisée de l'application. Veuillez confirmer votre mot de passe avant de continuer.</p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div class="field">
                <label for="password">Mot de passe</label>
                <div class="field-input-wrap">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                Confirmer
            </button>
        </form>
    </div>
</x-guest-layout>
