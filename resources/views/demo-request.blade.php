<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Demandez une démonstration gratuite de ManageX — La plateforme RH intelligente.">
    <title>Demander une démo — ManageX</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script nonce="{{ $cspNonce ?? '' }}">
        // Theme initialization
        (function() {
            var saved = localStorage.getItem('managex-theme');
            var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <style nonce="{{ $cspNonce ?? '' }}">
        :root {
            /* Palette Premium Modern */
            --primary: #4F46E5; /* Indigo 600 */
            --primary-hover: #4338CA; /* Indigo 700 */
            --primary-light: #818CF8;
            --secondary: #10B981; /* Emerald 500 */
            --dark-bg: #0F172A; /* Slate 900 */
            --dark-surface: #1E293B; /* Slate 800 */
            --light-bg: #F8FAFC; /* Slate 50 */
            --light-surface: #FFFFFF;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --border-light: #E2E8F0;
            --border-dark: #334155;
            
            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            --gradient-glow: radial-gradient(circle at center, rgba(79, 70, 229, 0.15) 0%, transparent 70%);
            
            /* Shadows & Blur */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
            --glass-blur: blur(12px);
        }

        [data-theme="dark"] {
            --bg-body: var(--dark-bg);
            --bg-surface: var(--dark-surface);
            --text-main: #F1F5F9;
            --text-muted: #94A3B8;
            --border: var(--border-dark);
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        [data-theme="light"] {
            --bg-body: var(--light-bg);
            --bg-surface: var(--light-surface);
            --text-main: #0F172A;
            --text-muted: #64748B;
            --border: var(--border-light);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.5;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Background Graphics */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.4;
        }
        .bg-orb-1 { top: -10%; right: -5%; width: 600px; height: 600px; background: radial-gradient(circle, #818CF8 0%, rgba(129, 140, 248, 0) 70%); }
        .bg-orb-2 { bottom: -10%; left: -10%; width: 500px; height: 500px; background: radial-gradient(circle, #34D399 0%, rgba(52, 211, 153, 0) 70%); }

        /* Navigation */
        .nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5%;
            z-index: 50;
            background: rgba(255, 255, 255, 0.01);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        [data-theme="light"] .nav { background: rgba(255, 255, 255, 0.8); border-bottom: 1px solid var(--border-light); }

        .logo {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--text-main);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .logo-mark {
            width: 40px; height: 40px;
            background: var(--gradient-primary);
            border-radius: 12px;
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
        }

        .cta-button {
            padding: 0.75rem 1.5rem;
            background: var(--gradient-primary);
            color: white;
            border-radius: 9999px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
        }

        /* Layout */
        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 20px 60px;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        /* Left Content */
        .content h1 {
            font-size: 3.5rem;
            line-height: 1.1;
            font-weight: 800;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }
        .content h1 span {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .lead-text {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            max-width: 90%;
        }

        .benefits-list {
            list-style: none;
            display: grid;
            gap: 1.5rem;
        }
        .benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .check-icon {
            flex-shrink: 0;
            width: 24px; height: 24px;
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .benefit-content h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .benefit-content p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Right Form */
        .form-card {
            background: var(--bg-surface);
            padding: 2.5rem;
            border-radius: 24px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-xl);
            position: relative;
            overflow: hidden;
        }
        /* Top accent border */
        .form-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: var(--gradient-primary);
        }

        .form-group { margin-bottom: 1.5rem; position: relative; }
        
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text-main);
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 12px;
            background: var(--gradient-primary);
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
        }
        
        .submit-btn::after {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(rgba(255,255,255,0.2), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .submit-btn:hover::after { opacity: 1; }

        /* Trust Badges */
        .trust-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
            text-align: center;
        }
        .trust-text {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .trust-logos {
            display: flex;
            justify-content: center;
            gap: 2rem;
            opacity: 0.6;
            filter: grayscale(100%);
        }

        /* Success Message */
        .success-overlay {
            position: absolute;
            inset: 0;
            background: var(--bg-surface);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 10;
            animation: fadeIn 0.5s ease;
        }
        .success-icon {
            width: 80px; height: 80px;
            background: #D1FAE5;
            color: #10B981;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
        }
        .success-icon svg { width: 40px; height: 40px; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 968px) {
            .container { grid-template-columns: 1fr; gap: 3rem; }
            .content { text-align: center; }
            .lead-text { margin: 0 auto 2.5rem; }
            .benefits-list { text-align: left; max-width: 500px; margin: 0 auto; }
            .content h1 { font-size: 2.5rem; }
        }
        @media (max-width: 640px) {
            .form-row { grid-template-columns: 1fr; }
            .nav { padding: 0 1.5rem; } 
        }
    </style>
</head>
<body>

    <!-- Particles.js Background -->
    <div id="particles-js" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;"></div>

    <!-- Background Decoration -->
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>

    <nav class="nav">
       <a href="{{ url('/') }}" class="nav-logo">
        <span class="nav-logo-icon">
          <img src="{{ asset('images/managex_logo.png') }}" alt="" style="width:28px;height:28px;border-radius:50%;object-fit:cover">
        </span>
        <span class="text-gradient">ManageX</span>
      </a>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ url('/') }}" style="color: var(--text-muted); text-decoration: none; font-weight: 500; display: none; @media(min-width:768px){display:block;}">Retour</a>
            <a href="{{ route('login') }}" class="cta-button">Connexion</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="container">
            <!-- Left Side: Engaging Content -->
            <div class="content">
                <h1>Transformez votre <br><span>Gestion RH</span></h1>
                <p class="lead-text">
                    Rejoignez les leaders qui simplifient la paie, les congés et la performance avec ManageX. Une plateforme unique pour une efficacité maximale.
                </p>

                <ul class="benefits-list">
                    <li class="benefit-item">
                        <div class="check-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="benefit-content">
                            <h3>Tout-en-un intuitif</h3>
                            <p>Paie, congés, présences et évaluations dans une interface fluide.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="check-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="benefit-content">
                            <h3>Automatisation IA</h3>
                            <p>Gagnez 40% de temps administratif grâce à nos assistants intelligents.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="check-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="benefit-content">
                            <h3>Support Premium 24/7</h3>
                            <p>Une équipe dédiée pour vous accompagner à chaque étape.</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Right Side: Contact Form -->
            <div class="form-card" x-data="{ submitting: false }">
                
                @if(session('success'))
                <div class="success-overlay">
                    <div class="success-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Message Reçu !</h2>
                    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Notre équipe d'experts vous contactera sous 24h ouvrées.</p>
                    <a href="{{ url('/') }}" class="cta-button" style="display: inline-block;">Retour à l'accueil</a>
                </div>
                @endif

                <h2 style="font-size: 1.75rem; margin-bottom: 0.5rem; font-weight: 700;">Réserver une démo</h2>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">Remplissez ce formulaire pour une présentation sur mesure.</p>

                <form action="{{ route('demo-request.store') }}" method="POST" @submit="submitting = true">
                    @csrf
                    
                    @if($errors->any())
                    <div style="background: #FEF2F2; color: #991B1B; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                        <ul style="padding-left: 1rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Votre Nom</label>
                            <input type="text" name="contact_name" class="form-input" placeholder="Jean Dupont" required value="{{ old('contact_name') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Pro</label>
                            <input type="email" name="email" class="form-input" placeholder="jean@entreprise.com" required value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Entreprise</label>
                            <input type="text" name="company_name" class="form-input" placeholder="Tech Solutions" required value="{{ old('company_name') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Taille</label>
                            <select name="company_size" class="form-select" required>
                                <option value="" disabled selected>Sélectionner...</option>
                                <option value="1-10">1-10 employés</option>
                                <option value="11-50">11-50 employés</option>
                                <option value="51-200">51-200 employés</option>
                                <option value="200+">200+ employés</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" name="phone" class="form-input" placeholder="+225 07 00 00 00" value="{{ old('phone') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Message (Optionnel)</label>
                        <textarea name="message" class="form-textarea" rows="3" placeholder="Vos besoins spécifiques...">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="submit-btn" :disabled="submitting">
                        <span x-show="!submitting">Obtenir ma démo gratuite</span>
                        <span x-show="submitting" style="display: none;">Envoi en cours...</span>
                    </button>
                    
                    <p style="text-align: center; color: var(--text-muted); font-size: 0.8rem; margin-top: 1rem;">
                        Aucune carte bancaire requise. Sans engagement.
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof particlesJS !== 'undefined') {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                const color = isDark ? '#ffffff' : '#4F46E5';

                particlesJS('particles-js', {
                    particles: {
                        number: { value: 50, density: { enable: true, value_area: 800 } },
                        color: { value: color },
                        shape: { type: 'circle' },
                        opacity: { value: 0.12, random: true, anim: { enable: true, speed: 0.6, opacity_min: 0.04, sync: false } },
                        size: { value: 3, random: true, anim: { enable: true, speed: 2, size_min: 0.5, sync: false } },
                        line_linked: { enable: true, distance: 130, color: color, opacity: 0.07, width: 1 },
                        move: { enable: true, speed: 1, direction: 'none', random: true, straight: false, out_mode: 'out', bounce: false }
                    },
                    interactivity: {
                        detect_on: 'window',
                        events: { onhover: { enable: true, mode: 'grab' }, onclick: { enable: true, mode: 'push' }, resize: true },
                        modes: { grab: { distance: 140, line_linked: { opacity: 0.2 } }, push: { particles_nb: 3 } }
                    },
                    retina_detect: true
                });
            }
        });
    </script>
</body>
</html>
