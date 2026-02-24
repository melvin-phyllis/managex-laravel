<x-guest-layout>
    <div class="form-card anim-1">
        <h2 class="form-title">Connexion</h2>
        <p class="form-subtitle">Entrez vos identifiants pour accéder à votre espace</p>

        <form method="POST" action="{{ route('login') }}" x-data="{ loading: false, showPass: false }" @submit="loading = true">
            @csrf

            <!-- Email -->
            <div class="field anim-2">
                <label for="email">Adresse email</label>
                <div class="field-input-wrap">
                    <svg viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        placeholder="votre@email.com">
                </div>
            </div>

            <!-- Password -->
            <div class="field anim-3">
                <label for="password">Mot de passe</label>
                <div class="field-input-wrap">
                    <svg viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <input id="password" :type="showPass ? 'text' : 'password'" name="password" required autocomplete="current-password"
                        placeholder="••••••••">
                    <button type="button" class="toggle-password" @click="showPass = !showPass" x-text="showPass ? 'Masquer' : 'Afficher'">Afficher</button>
                </div>
            </div>

            <!-- Remember + Forgot -->
            <div class="options-row anim-3">
                <label for="remember_me" class="remember-check">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>Se souvenir de moi</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-submit anim-4" :disabled="loading">
                <template x-if="!loading">
                    <span>Se connecter</span>
                </template>
                <template x-if="loading">
                    <span style="display:flex;align-items:center;gap:.5rem">
                        <span class="spinner"></span>
                        Connexion en cours...
                    </span>
                </template>
            </button>
        </form>

        <div class="divider">
            <span>ou</span>
        </div>

        <p class="login-footer">
            Pas encore de compte ? <a href="{{ route('demo-request') }}">Demander un accès</a>
        </p>
    </div>

    {{-- Crédit caché visuellement mais présent dans le HTML : indexable par Google, trouvable via Ctrl+F --}}
    <div class="hidden" aria-hidden="true" style="display:none">
        <p>
            Projet réalisé par
            <a href="https://github.com/melvin-phyllis" target="_blank" rel="noopener noreferrer" title="Akou Melvin — GitHub">Akou Melvin</a>
            <a href="https://www.linkedin.com/in/melvin-akou/" target="_blank" rel="noopener noreferrer" title="LinkedIn">LinkedIn</a>
        </p>
    </div>
    {{-- Auto-refresh CSRF token to prevent 419 "Page Expired" --}}
    <script>
        setInterval(function() {
            fetch('/ManageX/public/sanctum/csrf-cookie', { method: 'GET', credentials: 'same-origin' })
                .catch(function() {
                    // If sanctum not available, just reload the page
                    var token = document.querySelector('input[name="_token"]');
                    if (token) {
                        fetch(window.location.href, { credentials: 'same-origin' })
                            .then(function(r) { return r.text(); })
                            .then(function(html) {
                                var match = html.match(/name="_token"[^>]*value="([^"]+)"/);
                                if (match) token.value = match[1];
                            }).catch(function() { window.location.reload(); });
                    }
                });
        }, 30 * 60 * 1000); // Toutes les 30 minutes
    </script>
</x-guest-layout>
