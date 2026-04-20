<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ManageX') }} - Connexion</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1B3C35">
    <meta name="description" content="ManageX - Application de gestion des ressources humaines">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ route('manifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="ManageX">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Serif+Display&display=swap" rel="stylesheet">

    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *,*::before,*::after{box-sizing:border-box}
        :root{
            --green:#1B3C35;--green-light:#2D5A4E;--green-accent:#3D7A6A;
            --cream:#F5F0E8;--cream-light:#FAF7F2;--cream-dark:#ECE5D8;
            --gold:#C8A96E;--gold-light:#D4BC8B;
            --fg:#1B3C35;--muted:#5C6E68;
        }

        body{
            font-family:'DM Sans',system-ui,sans-serif;
            margin:0;padding:0;min-height:100vh;
            background:var(--cream);
            color:var(--fg);
            display:flex;
            overflow-x:hidden;
        }

        /* ─── LAYOUT ─── */
        .login-page{
            display:flex;
            width:100%;
            min-height:100vh;
        }

        /* ─── LEFT: Decorative side ─── */
        .login-deco{
            flex:1.1;
            background:var(--green);
            position:relative;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            padding:3rem;
            overflow:hidden;
        }
        .login-deco-orb{
            position:absolute;
            border-radius:50%;
            filter:blur(80px);
            pointer-events:none;
        }
        .login-deco-content{
            position:relative;
            z-index:2;
            text-align:center;
            max-width:400px;
        }
        .login-deco-logo{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:.75rem;
            margin-bottom:3rem;
        }
        .login-deco-logo img{
            height:80px;width:auto;object-fit:contain;
        }
        .login-deco-logo span{
            font-size:1.3rem;font-weight:700;color:var(--cream);
            font-family:'DM Serif Display',serif;
        }
        .login-deco h2{
            font-family:'DM Serif Display',serif;
            font-size:clamp(1.6rem,3vw,2.2rem);
            color:var(--cream);
            line-height:1.2;
            margin-bottom:1rem;
        }
        .login-deco p{
            font-size:.9rem;
            color:rgba(245,240,232,.55);
            line-height:1.7;
        }

        /* Decorative shapes */
        .deco-shape{
            position:absolute;
            pointer-events:none;
            z-index:1;
        }
        .deco-dots{
            width:80px;height:80px;
            background-image:radial-gradient(circle,rgba(245,240,232,.15) 2px,transparent 2px);
            background-size:12px 12px;
        }
        .deco-line{
            width:60px;height:2px;
            background:rgba(245,240,232,.12);
            border-radius:1px;
        }
        .deco-circle{
            width:100px;height:100px;
            border:2px solid rgba(245,240,232,.08);
            border-radius:50%;
        }
        .deco-rect{
            width:70px;height:50px;
            border:2px solid rgba(245,240,232,.07);
            border-radius:8px;
        }
        .deco-squiggle{
            color:rgba(245,240,232,.12);
            font-size:2rem;
        }

        /* Stats on deco side */
        .deco-stats{
            display:flex;gap:2rem;margin-top:2.5rem;
        }
        .deco-stat{
            text-align:center;
        }
        .deco-stat-value{
            font-family:'DM Serif Display',serif;
            font-size:1.8rem;
            color:var(--gold);
        }
        .deco-stat-label{
            font-size:.7rem;
            color:rgba(245,240,232,.4);
            text-transform:uppercase;
            letter-spacing:.08em;
            margin-top:.25rem;
        }

        .deco-email{
            position:absolute;
            bottom:2.5rem;
            left:3rem;
            font-size:.8rem;
            color:rgba(245,240,232,.3);
            display:flex;align-items:center;gap:.35rem;
            z-index:2;
        }
        .deco-email svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:1.5}

        /* ─── RIGHT: Form side ─── */
        .login-form-side{
            flex:1;
            display:flex;
            flex-direction:column;
            justify-content:flex-start; /* Start from top to handle long forms */
            align-items:center;
            padding:5rem 2rem; /* More vertical padding for spacing */
            position:relative;
            background:var(--cream);
            min-height:100vh;
            overflow-y:auto;
        }

        .login-form-wrapper{
            width:100%;
            max-width:420px;
        }

        /* Nav on form side */
        .login-topbar{
            position:absolute;
            top:0;left:0;right:0;
            display:flex;
            align-items:center;
            justify-content:flex-end;
            gap:.75rem;
            padding:1.5rem 2.5rem;
        }
        .login-topbar a{
            font-size:.85rem;
            font-weight:500;
            text-decoration:none;
            transition:all .2s;
        }
        .login-topbar .link{color:var(--muted)}
        .login-topbar .link:hover{color:var(--green)}
        .login-topbar .btn-cta{
            padding:.55rem 1.25rem;
            border-radius:999px;
            background:var(--gold);
            color:var(--green);
            font-weight:600;
            font-size:.8rem;
            border:none;
            cursor:pointer;
            transition:all .25s;
        }
        .login-topbar .btn-cta:hover{
            background:var(--gold-light);
            transform:translateY(-1px);
        }

        .form-card{
            background:#fff;
            border-radius:1.25rem;
            padding:2.5rem;
            box-shadow:0 1px 3px rgba(27,60,53,.06),0 20px 60px rgba(27,60,53,.08);
            border:1px solid var(--cream-dark);
        }
        .form-title{
            font-family:'DM Serif Display',serif;
            font-size:1.75rem;
            color:var(--green);
            text-align:center;
            margin-bottom:.35rem;
        }
        .form-subtitle{
            text-align:center;
            font-size:.875rem;
            color:var(--muted);
            margin-bottom:2rem;
        }

        /* ─── FORM FIELDS ─── */
        .field{margin-bottom:1.25rem}
        .field label{
            display:block;
            font-size:.8rem;
            font-weight:600;
            color:var(--muted);
            margin-bottom:.4rem;
            text-transform:uppercase;
            letter-spacing:.04em;
        }
        .field-input-wrap{
            position:relative;
        }
        .field-input-wrap svg{
            position:absolute;
            left:.85rem;top:50%;
            transform:translateY(-50%);
            width:18px;height:18px;
            stroke:var(--muted);
            fill:none;stroke-width:1.5;
            opacity:.5;
            pointer-events:none;
        }
        .field input[type="email"],
        .field input[type="password"],
        .field input[type="text"]{
            width:100%;
            padding:.85rem .85rem .85rem 2.75rem;
            border:1.5px solid var(--cream-dark);
            border-radius:.75rem;
            font-family:inherit;
            font-size:.9rem;
            color:var(--fg);
            background:var(--cream-light);
            outline:none;
            transition:all .2s;
        }
        .field input:focus{
            border-color:var(--green-accent);
            background:#fff;
            box-shadow:0 0 0 3px rgba(61,122,106,.1);
        }
        .field input::placeholder{color:rgba(92,110,104,.4)}

        .toggle-password{
            position:absolute;
            right:.85rem;top:50%;transform:translateY(-50%);
            background:none;border:none;
            font-size:.75rem;font-weight:600;
            color:var(--muted);cursor:pointer;
            font-family:inherit;
            transition:color .2s;
        }
        .toggle-password:hover{color:var(--green)}

        /* ─── OPTIONS ROW ─── */
        .options-row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:1.5rem;
        }
        .remember-check{
            display:flex;align-items:center;gap:.5rem;
            cursor:pointer;
        }
        .remember-check input[type="checkbox"]{
            width:16px;height:16px;
            border:1.5px solid var(--cream-dark);
            border-radius:4px;
            accent-color:var(--green);
            cursor:pointer;
        }
        .remember-check span{
            font-size:.825rem;
            color:var(--muted);
        }
        .forgot-link{
            font-size:.8rem;
            color:var(--green-accent);
            font-weight:500;
            text-decoration:none;
            transition:color .2s;
        }
        .forgot-link:hover{color:var(--green)}

        /* ─── SUBMIT ─── */
        .btn-submit{
            width:100%;
            padding:.9rem;
            border:none;
            border-radius:999px;
            background:var(--gold);
            color:var(--green);
            font-family:inherit;
            font-size:.95rem;
            font-weight:700;
            cursor:pointer;
            transition:all .25s;
            display:flex;align-items:center;justify-content:center;gap:.5rem;
            box-shadow:0 4px 14px rgba(200,169,110,.35);
        }
        .btn-submit:hover{
            background:var(--gold-light);
            transform:translateY(-1px);
            box-shadow:0 6px 20px rgba(200,169,110,.4);
        }
        .btn-submit:active{transform:translateY(0)}
        .btn-submit:disabled{
            opacity:.7;cursor:not-allowed;transform:none;
        }

        /* ─── DIVIDER ─── */
        .divider{
            display:flex;align-items:center;gap:1rem;
            margin:1.5rem 0;
        }
        .divider::before,.divider::after{
            content:"";flex:1;height:1px;
            background:var(--cream-dark);
        }
        .divider span{
            font-size:.75rem;color:var(--muted);
            white-space:nowrap;
        }

        /* ─── FOOTER ─── */
        .login-footer{
            text-align:center;
            margin-top:1.5rem;
            font-size:.825rem;
            color:var(--muted);
        }
        .login-footer a{
            color:var(--green);
            font-weight:600;
            text-decoration:underline;
            text-decoration-color:var(--cream-dark);
            text-underline-offset:2px;
            transition:text-decoration-color .2s;
        }
        .login-footer a:hover{text-decoration-color:var(--green)}

        .login-copyright{
            position:absolute;
            bottom:1.5rem;left:0;right:0;
            text-align:center;
            font-size:.725rem;
            color:rgba(92,110,104,.4);
        }
        .login-copyright a{
            color:rgba(92,110,104,.5);
            text-decoration:none;
        }

        /* ─── ANIMATIONS ─── */
        @keyframes fadeUp{0%{opacity:0;transform:translateY(20px)}100%{opacity:1;transform:translateY(0)}}
        @keyframes fadeIn{0%{opacity:0}100%{opacity:1}}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
        .anim-1{animation:fadeUp .6s ease-out both}
        .anim-2{animation:fadeUp .6s ease-out .1s both}
        .anim-3{animation:fadeUp .6s ease-out .2s both}
        .anim-4{animation:fadeUp .6s ease-out .3s both}

        /* ─── RESPONSIVE ─── */
        @media(max-width:900px){
            .login-deco{display:none}
            .login-form-side{padding:2rem 1.25rem}
            .login-topbar{padding:1rem 1.25rem}
        }
        @media(max-width:480px){
            .form-card{padding:1.75rem 1.25rem}
            .form-title{font-size:1.5rem}
        }

        /* Spinner for loading */
        @keyframes spin{to{transform:rotate(360deg)}}
        .spinner{
            width:20px;height:20px;
            border:2.5px solid rgba(27,60,53,.2);
            border-top-color:var(--green);
            border-radius:50%;
            animation:spin .6s linear infinite;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <!-- ═══ LEFT DECO PANEL ═══ -->
        <div class="login-deco">
            <!-- Orbs -->
            <div class="login-deco-orb" style="width:300px;height:300px;top:-60px;left:-80px;background:rgba(61,122,106,.3)"></div>
            <div class="login-deco-orb" style="width:250px;height:250px;bottom:-40px;right:-60px;background:rgba(200,169,110,.2)"></div>

            <!-- Decorative shapes -->
            <div class="deco-shape deco-dots" style="top:15%;left:8%"></div>
            <div class="deco-shape deco-dots" style="bottom:18%;right:10%"></div>
            <div class="deco-shape deco-circle" style="top:20%;right:15%;animation:float 7s ease-in-out infinite"></div>
            <div class="deco-shape deco-rect" style="bottom:25%;left:12%;animation:float 8s ease-in-out infinite 1s"></div>
            <div class="deco-shape deco-line" style="top:40%;left:5%"></div>
            <div class="deco-shape deco-line" style="bottom:35%;right:8%;width:80px"></div>
            <div class="deco-shape deco-squiggle" style="top:12%;right:25%">〰</div>
            <div class="deco-shape deco-squiggle" style="bottom:15%;left:20%">〰</div>
            <div class="deco-shape deco-rect" style="top:55%;right:8%;width:50px;height:35px;animation:float 9s ease-in-out infinite 2s"></div>

            <!-- Content -->
            <div class="login-deco-content">
                <div class="login-deco-logo">
                    <img src="{{ asset('images/managex_logo.png') }}" alt="ManageX Logo">
                </div>
                <h2>Gérez vos équipes avec simplicité et efficacité</h2>
                <p>Présences, tâches, congés, paie — tout dans une seule plateforme intuitive propulsée par l'IA.</p>

                <div class="deco-stats">
                    <div class="deco-stat">
                        <div class="deco-stat-value">150+</div>
                        <div class="deco-stat-label">Entreprises</div>
                    </div>
                    <div class="deco-stat">
                        <div class="deco-stat-value">5K+</div>
                        <div class="deco-stat-label">Employés</div>
                    </div>
                    <div class="deco-stat">
                        <div class="deco-stat-value">99.9%</div>
                        <div class="deco-stat-label">Uptime</div>
                    </div>
                </div>
            </div>

            <div class="deco-email">
                <svg viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                contact@ya-consulting.com
            </div>
        </div>

        <!-- ═══ RIGHT FORM PANEL ═══ -->
        <div class="login-form-side">
            <!-- Top bar -->
            <div class="login-topbar">
                <a href="{{ url('/') }}" class="link">← Accueil</a>
                <a href="{{ route('register') }}" class="btn-cta">S'inscrire</a>
            </div>

            <div class="login-form-wrapper">
                {{ $slot }}
            </div>

            <div class="login-copyright">
                &copy; {{ date('Y') }} ManageX — <a href="https://ya-consulting.com" target="_blank">YA Consulting</a> &nbsp;|&nbsp; Politique de confidentialité
            </div>
        </div>
    </div>

    <!-- Toastify JS -->
    <script nonce="{{ $cspNonce ?? '' }}" type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script nonce="{{ $cspNonce ?? '' }}">
        document.addEventListener('DOMContentLoaded', function() {
            // Success Toast
            @if(session('success') || session('status'))
                Toastify({
                    text: "{{ session('success') ?? session('status') }}",
                    duration: 4000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(to right, #2D5A4E, #3D7A6A)",
                        borderRadius: "10px",
                        boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)",
                    },
                }).showToast();
            @endif

            // Error Toast
            @if(session('error'))
                Toastify({
                    text: "{{ session('error') }}",
                    duration: 4000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(to right, #ef4444, #b91c1c)",
                        borderRadius: "10px",
                        boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)",
                    },
                }).showToast();
            @endif

            // Validation Errors
            @if($errors->any())
                @if($errors->has('email') || $errors->has('password'))
                    Toastify({
                        text: "Identification de connexion incorrecte",
                        duration: 4000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background: "linear-gradient(to right, #ef4444, #b91c1c)",
                            borderRadius: "10px",
                            boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)",
                        },
                    }).showToast();
                @endif
            @endif
        });
    </script>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset("sw.js") }}')
                    .then((registration) => {
                        console.log('ManageX SW registered:', registration.scope);
                    })
                    .catch((error) => {
                        console.log('ManageX SW registration failed:', error);
                    });
            });
        }
    </script>
</body>
</html>
