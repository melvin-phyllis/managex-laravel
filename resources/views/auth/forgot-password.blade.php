<x-guest-layout>
    <div class="form-card">
        <h1 class="form-title">Mot de passe oublié ?</h1>
        <p class="form-subtitle">Pas de souci, nous vous enverrons un lien de réinitialisation</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email -->
            <div class="field">
                <label for="email">Adresse email</label>
                <div class="field-input-wrap">
                    <svg viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="votre@email.com">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Envoyer le lien de réinitialisation
            </button>

            <!-- Back to login -->
            <div class="login-footer" style="margin-top:1.5rem">
                <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:.4rem">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Retour à la connexion
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
