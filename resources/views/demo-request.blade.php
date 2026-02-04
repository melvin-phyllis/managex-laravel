<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Demandez une démonstration gratuite de ManageX — La plateforme RH intelligente.">
    <title>Demander une démo — ManageX</title>
    <script>
        (function() {
            var saved = localStorage.getItem('managex-theme');
            var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <style>
        :root {
            --white: #ffffff;
            --off-white: #f8f9fc;
            --light: #f0f1f6;
            --light-surface: #e8e9f0;
            --black: #0f1023;
            --dark-text: #1a1b2e;
            --gray-300: #5a5b72;
            --gray-500: #8b8ca2;
            --gray-600: #b0b1c4;
            --accent: #6c4cec;
            --accent-light: #7c5cfc;
            --accent-glow: rgba(108, 76, 236, 0.18);
            --border: rgba(0, 0, 0, 0.07);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.04);
            --shadow-md: 0 8px 30px rgba(0,0,0,0.06);
            --shadow-lg: 0 20px 60px rgba(0,0,0,0.08);
            --font-sans: 'Segoe UI', system-ui, -apple-system, sans-serif;
            --transition: cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        [data-theme="dark"] {
            --white: #13141f;
            --off-white: #181926;
            --light: #1e1f30;
            --light-surface: #252640;
            --black: #f0f1f6;
            --dark-text: #e8e9f0;
            --gray-300: #b0b1c4;
            --gray-500: #8b8ca2;
            --gray-600: #5a5b72;
            --border: rgba(255, 255, 255, 0.08);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.2);
            --shadow-md: 0 8px 30px rgba(0,0,0,0.3);
            --shadow-lg: 0 20px 60px rgba(0,0,0,0.4);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-sans);
            background: var(--off-white);
            color: var(--dark-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .nav .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--dark-text);
            font-weight: 700;
            font-size: 1.25rem;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--gray-300);
            font-size: 0.9rem;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-links a:hover {
            color: var(--accent);
            background: var(--accent-glow);
        }

        .nav-cta {
            background: var(--accent) !important;
            color: white !important;
            padding: 8px 20px !important;
            border-radius: 8px !important;
        }

        .nav-cta:hover {
            background: var(--accent-light) !important;
        }

        .theme-toggle {
            background: var(--light);
            border: 1px solid var(--border);
            border-radius: 8px;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--gray-300);
            transition: all 0.2s;
        }

        .theme-toggle:hover {
            color: var(--accent);
            border-color: var(--accent);
        }

        [data-theme="light"] .icon-moon { display: none; }
        [data-theme="dark"] .icon-sun { display: none; }

        .page-content {
            flex: 1;
            padding-top: 100px;
            padding-bottom: 60px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .demo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: start;
        }

        .demo-info h1 {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 16px;
            color: var(--dark-text);
        }

        .text-accent { color: var(--accent); }

        .demo-info > p {
            font-size: 1.1rem;
            color: var(--gray-300);
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .demo-benefits {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .demo-benefits li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.95rem;
            color: var(--gray-300);
            line-height: 1.5;
        }

        .benefit-icon {
            width: 28px;
            height: 28px;
            min-width: 28px;
            background: var(--accent-glow);
            color: var(--accent);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            margin-top: 1px;
        }

        .demo-form-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 36px;
            box-shadow: var(--shadow-lg);
        }

        .demo-form-card h2 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--dark-text);
        }

        .demo-form-card > p {
            color: var(--gray-500);
            font-size: 0.9rem;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 6px;
        }

        .form-group label .required {
            color: #ef4444;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 10px 14px;
            font-size: 0.9rem;
            font-family: inherit;
            color: var(--dark-text);
            background: var(--off-white);
            border: 1px solid var(--border);
            border-radius: 10px;
            outline: none;
            transition: all 0.2s;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: var(--gray-500);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%238b8ca2' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .btn-submit {
            width: 100%;
            padding: 13px 24px;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            color: white;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s var(--transition);
            margin-top: 6px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--accent-glow);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        [data-theme="dark"] .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.85rem;
        }

        [data-theme="dark"] .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        .alert-error ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .footer {
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 24px 0;
            text-align: center;
        }

        .footer-copy {
            color: var(--gray-500);
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .demo-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .demo-info h1 {
                font-size: 1.8rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }

            .demo-form-card {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="container">
            <a href="{{ url('/') }}" class="nav-logo">
                <div class="logo-icon">M</div>
                ManageX
            </a>
            <ul class="nav-links">
                <li><a href="{{ url('/') }}">Accueil</a></li>
                <li><a href="{{ url('/#features') }}">Fonctionnalités</a></li>
                <li><a href="{{ route('login') }}" class="nav-cta">Se connecter</a></li>
            </ul>
            <button class="theme-toggle" id="themeToggle" aria-label="Changer le thème">
                <svg class="icon-sun" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                <svg class="icon-moon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            </button>
        </div>
    </nav>

    <main class="page-content">
        <div class="container">
            <div class="demo-grid">
                <div class="demo-info">
                    <h1>Découvrez ManageX <span class="text-accent">en action</span></h1>
                    <p>Planifiez une démonstration personnalisée avec notre équipe. Nous vous montrerons comment ManageX peut transformer la gestion RH de votre entreprise.</p>

                    <ul class="demo-benefits">
                        <li>
                            <div class="benefit-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <span><strong>Démo personnalisée</strong> — Adaptée aux besoins spécifiques de votre entreprise et votre secteur d'activité.</span>
                        </li>
                        <li>
                            <div class="benefit-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <span><strong>Sans engagement</strong> — Découvrez la plateforme librement, posez toutes vos questions.</span>
                        </li>
                        <li>
                            <div class="benefit-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <span><strong>Présences, congés, paie, tâches</strong> — Voyez en direct comment tout se gère depuis une seule plateforme.</span>
                        </li>
                        <li>
                            <div class="benefit-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <span><strong>Intelligence IA intégrée</strong> — Chatbot RH, analytics prédictifs et recommandations automatiques.</span>
                        </li>
                        <li>
                            <div class="benefit-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <span><strong>Réponse sous 24h</strong> — Notre équipe vous recontacte rapidement pour planifier votre créneau.</span>
                        </li>
                    </ul>
                </div>

                <div class="demo-form-card">
                    <h2>Demander une démo gratuite</h2>
                    <p>Remplissez le formulaire et notre équipe vous contactera.</p>

                    @if(session('success'))
                        <div class="alert-success">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert-error">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('demo-request.store') }}" method="POST">
                        @csrf

                        <div class="form-row">
                            <div class="form-group">
                                <label>Nom complet <span class="required">*</span></label>
                                <input type="text" name="contact_name" class="form-input" placeholder="Jean Dupont" value="{{ old('contact_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Email professionnel <span class="required">*</span></label>
                                <input type="email" name="email" class="form-input" placeholder="jean@entreprise.com" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Nom de l'entreprise <span class="required">*</span></label>
                                <input type="text" name="company_name" class="form-input" placeholder="Votre entreprise" value="{{ old('company_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input type="tel" name="phone" class="form-input" placeholder="+225 07 00 00 00" value="{{ old('phone') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Taille de l'entreprise <span class="required">*</span></label>
                            <select name="company_size" class="form-select" required>
                                <option value="" disabled {{ old('company_size') ? '' : 'selected' }}>Sélectionner...</option>
                                <option value="1-10" {{ old('company_size') === '1-10' ? 'selected' : '' }}>1 - 10 employés</option>
                                <option value="11-50" {{ old('company_size') === '11-50' ? 'selected' : '' }}>11 - 50 employés</option>
                                <option value="51-200" {{ old('company_size') === '51-200' ? 'selected' : '' }}>51 - 200 employés</option>
                                <option value="200+" {{ old('company_size') === '200+' ? 'selected' : '' }}>200+ employés</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Message (optionnel)</label>
                            <textarea name="message" class="form-textarea" placeholder="Décrivez vos besoins ou posez vos questions...">{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn-submit">Demander ma démo gratuite</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-copy">&copy; {{ date('Y') }} ManageX. Tous droits réservés.</div>
        </div>
    </footer>

    <script>
        (function() {
            const html = document.documentElement;
            const themeToggle = document.getElementById('themeToggle');
            const STORAGE_KEY = 'managex-theme';

            function setTheme(theme) {
                html.setAttribute('data-theme', theme);
                localStorage.setItem(STORAGE_KEY, theme);
            }

            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const current = html.getAttribute('data-theme');
                    setTheme(current === 'dark' ? 'light' : 'dark');
                });
            }
        })();
    </script>
</body>
</html>
