<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>ManageX — Gestion RH intelligente</title>
  <meta name="description" content="ManageX centralise présences, tâches, congés, paie, sondages et statistiques dans un seul dashboard clair et moderne." />
  <meta name="theme-color" content="#5680E9">
  <link rel="manifest" href="{{ route('manifest') }}">
  <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
    :root{
      --primary:#5680E9;--secondary:#5AB9EA;--tertiary:#84CEEB;--accent:#8860D0;
      --border:#C1C8E4;--bg:#f8fafc;--card:#fff;--fg:#1e293b;--muted:#64748b;
      --shadow-soft:0 10px 30px -18px rgba(86,128,233,.35);
      --shadow-md:0 20px 50px -20px rgba(86,128,233,.22);
      --gradient-hero:linear-gradient(135deg,#5680E9 0%,#84CEEB 60%,#5AB9EA 100%);
      --gradient-logo:linear-gradient(90deg,#3158a8,#5AB9EA);
      --radius:0.9rem;--radius-lg:1.25rem;
    }
    html{scroll-behavior:smooth;font-size:16px;-webkit-font-smoothing:antialiased}
    body{font-family:Figtree,system-ui,sans-serif;background:var(--bg);color:var(--fg);line-height:1.6;overflow-x:hidden}
    a{text-decoration:none;color:inherit}
    .container{max-width:1200px;margin:0 auto;padding:0 1.5rem}

    /* Animations */
    @keyframes fadeUp{0%{opacity:0;transform:translateY(18px)}100%{opacity:1;transform:translateY(0)}}
    @keyframes fadeIn{0%{opacity:0}100%{opacity:1}}
    @keyframes scaleIn{0%{opacity:0;transform:scale(.96)}100%{opacity:1;transform:scale(1)}}
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
    @keyframes pulse-glow{0%,100%{opacity:.5}50%{opacity:.8}}
    @keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
    @keyframes counter{0%{opacity:0;transform:translateY(8px)}100%{opacity:1;transform:translateY(0)}}
    .animate-fadeUp{animation:fadeUp .5s ease-out both}
    .animate-fadeIn{animation:fadeIn .6s ease-out both}
    .animate-scaleIn{animation:scaleIn .2s ease-out both}
    .delay-1{animation-delay:.1s}.delay-2{animation-delay:.2s}.delay-3{animation-delay:.3s}.delay-4{animation-delay:.4s}

    /* ✨ Magic scroll reveal */
    .reveal{opacity:0;transform:translateY(30px) scale(.97);filter:blur(6px);transition:all .7s cubic-bezier(.16,1,.3,1)}
    .reveal.visible{opacity:1;transform:translateY(0) scale(1);filter:blur(0)}
    .reveal-left{opacity:0;transform:translateX(-40px) scale(.97);filter:blur(6px);transition:all .7s cubic-bezier(.16,1,.3,1)}
    .reveal-left.visible{opacity:1;transform:translateX(0) scale(1);filter:blur(0)}
    .reveal-right{opacity:0;transform:translateX(40px) scale(.97);filter:blur(6px);transition:all .7s cubic-bezier(.16,1,.3,1)}
    .reveal-right.visible{opacity:1;transform:translateX(0) scale(1);filter:blur(0)}
    .reveal-scale{opacity:0;transform:scale(.88);filter:blur(8px);transition:all .8s cubic-bezier(.16,1,.3,1)}
    .reveal-scale.visible{opacity:1;transform:scale(1);filter:blur(0)}
    @media(prefers-reduced-motion:reduce){.reveal,.reveal-left,.reveal-right,.reveal-scale{opacity:1;transform:none;filter:none;transition:none}}

    /* Link underline effect */
    .link-hover{position:relative;display:inline-block}
    .link-hover::after{content:"";position:absolute;left:0;bottom:-2px;height:2px;width:100%;transform:scaleX(0);transform-origin:bottom right;background:var(--primary);transition:transform .25s ease}
    .link-hover:hover::after{transform:scaleX(1);transform-origin:bottom left}

    /* Gradient text */
    .text-gradient{background:var(--gradient-logo);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

    /* Nav */
    .nav{position:sticky;top:0;z-index:50;background:rgba(255,255,255,.82);backdrop-filter:blur(16px);border-bottom:1px solid rgba(193,200,228,.5);transition:all .3s}
    .nav-inner{display:flex;align-items:center;justify-content:space-between;height:64px}
    .nav-logo{display:inline-flex;align-items:center;gap:.5rem;font-size:1rem;font-weight:700}
    .nav-logo-icon{width:36px;height:36px;border-radius:var(--radius);background:rgba(86,128,233,.1);border:1px solid rgba(193,200,228,.5);display:flex;align-items:center;justify-content:center}
    .nav-links{display:flex;align-items:center;gap:2rem;font-size:.875rem;color:var(--muted)}
    .nav-ctas{display:flex;align-items:center;gap:.5rem}
    .nav-mobile{display:none;background:none;border:none;cursor:pointer;padding:.5rem}

    /* Buttons */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.75rem 1.5rem;border-radius:var(--radius);font-size:.875rem;font-weight:600;border:none;cursor:pointer;transition:all .2s}
    .btn:hover{transform:translateY(-2px)}
    .btn-primary{background:var(--primary);color:#fff;box-shadow:var(--shadow-soft)}
    .btn-primary:hover{box-shadow:var(--shadow-md)}
    .btn-outline{background:#fff;border:1px solid rgba(193,200,228,.6);box-shadow:var(--shadow-soft)}

    /* Cards */
    .card{background:var(--card);border:1px solid rgba(193,200,228,.5);border-radius:var(--radius);padding:1.5rem;box-shadow:var(--shadow-soft);transition:all .25s}
    .card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md);border-color:rgba(86,128,233,.25)}

    /* Stat mini-cards */
    .stat-card{background:rgba(255,255,255,.85);border:1px solid rgba(193,200,228,.5);border-radius:var(--radius);padding:1rem 1.25rem;box-shadow:var(--shadow-soft);transition:all .25s}
    .stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
    .stat-label{font-size:.75rem;color:var(--muted);margin-bottom:.25rem}
    .stat-value{font-size:1.35rem;font-weight:700;letter-spacing:-.02em}

    /* Feature cards */
    .feature-icon{width:44px;height:44px;border-radius:var(--radius);display:flex;align-items:center;justify-content:center;margin-bottom:1rem;font-size:1.25rem}
    .feature-title{font-size:1rem;font-weight:600;margin-bottom:.35rem}
    .feature-desc{font-size:.875rem;color:var(--muted);line-height:1.6}

    /* Hero */
    .hero{position:relative;overflow:hidden;padding:4rem 0 5rem}
    .hero-grid{display:grid;gap:3rem;align-items:center}
    .hero-badge{display:inline-flex;align-items:center;gap:.5rem;padding:.4rem 1rem;border-radius:999px;background:rgba(255,255,255,.7);border:1px solid rgba(193,200,228,.5);font-size:.75rem;color:var(--muted);box-shadow:var(--shadow-soft)}
    .hero-badge-dot{width:6px;height:6px;border-radius:50%;background:var(--primary);animation:pulse-glow 2s infinite}
    .hero-title{font-size:clamp(2rem,5vw,3.2rem);font-weight:800;line-height:1.12;letter-spacing:-.03em;margin-top:1.25rem}
    .hero-subtitle{font-size:clamp(.95rem,1.5vw,1.15rem);color:var(--muted);max-width:520px;margin-top:1rem;line-height:1.7}
    .hero-ctas{display:flex;gap:.75rem;margin-top:2rem;flex-wrap:wrap}
    .hero-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-top:2.5rem}
    .hero-visual{position:relative}
    .hero-visual-glow{position:absolute;inset:-12px;border-radius:var(--radius-lg);background:var(--gradient-hero);opacity:.25;filter:blur(40px);z-index:-1}
    .hero-preview{border-radius:var(--radius-lg);border:1px solid rgba(193,200,228,.5);overflow:hidden;background:#fff;box-shadow:var(--shadow-md)}
    .hero-preview-bar{display:flex;align-items:center;gap:.375rem;padding:.75rem 1rem;border-bottom:1px solid rgba(193,200,228,.4);background:rgba(248,250,252,.95)}
    .hero-preview-dot{width:8px;height:8px;border-radius:50%}
    .hero-preview-body{padding:1.25rem}
    .hero-preview-row{display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-bottom:1rem}
    .hero-kpi{text-align:center;padding:.75rem;border-radius:.6rem;border:1px solid rgba(193,200,228,.35)}
    .hero-kpi-val{font-size:1.1rem;font-weight:700}
    .hero-kpi-label{font-size:.65rem;color:var(--muted);margin-top:.15rem}
    .hero-chart{display:flex;align-items:flex-end;gap:3px;height:60px;padding-top:.5rem}
    .hero-chart-bar{flex:1;border-radius:3px 3px 0 0;transition:height .6s ease;background:var(--primary);opacity:.7}

    /* Orbs */
    .orb{position:absolute;border-radius:50%;filter:blur(60px);pointer-events:none;z-index:0}
    .orb-1{width:300px;height:300px;top:-100px;left:-80px;background:rgba(136,96,208,.2);animation:float 8s ease-in-out infinite}
    .orb-2{width:350px;height:350px;top:30px;right:-120px;background:rgba(90,185,234,.25);animation:float 10s ease-in-out infinite 1s}
    .orb-3{width:250px;height:250px;bottom:-80px;left:30%;background:rgba(86,128,233,.18);animation:float 7s ease-in-out infinite 2s}

    /* Section headers */
    .section{padding:5rem 0}
    .section-tag{font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--primary);margin-bottom:.5rem;display:block}
    .section-title{font-size:clamp(1.5rem,3vw,2.2rem);font-weight:700;letter-spacing:-.02em;line-height:1.2}
    .section-desc{font-size:.95rem;color:var(--muted);margin-top:.75rem;max-width:520px;line-height:1.7}

    /* Features grid */
    .features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:2.5rem}

    /* AI Section */
    .ai-section{background:rgba(255,255,255,.5);border-top:1px solid rgba(193,200,228,.4);border-bottom:1px solid rgba(193,200,228,.4)}
    .ai-grid{display:grid;gap:3rem;align-items:center}
    .ai-features{list-style:none;margin-top:1.5rem;display:flex;flex-direction:column;gap:.75rem}
    .ai-feature{display:flex;gap:.75rem;font-size:.875rem;color:var(--muted);align-items:flex-start}
    .ai-feature-icon{width:28px;height:28px;border-radius:.5rem;background:rgba(86,128,233,.08);border:1px solid rgba(193,200,228,.5);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.75rem;margin-top:.1rem}
    .ai-mock{border-radius:var(--radius-lg);border:1px solid rgba(193,200,228,.5);background:#fff;box-shadow:var(--shadow-soft);overflow:hidden}
    .ai-mock-header{padding:1rem 1.25rem;border-bottom:1px solid rgba(193,200,228,.4);display:flex;align-items:center;justify-content:space-between}
    .ai-mock-body{padding:1.25rem;display:flex;flex-direction:column;gap:.75rem}
    .ai-bubble{padding:.75rem 1rem;border-radius:var(--radius);font-size:.875rem;line-height:1.6}
    .ai-bubble-user{background:rgba(86,128,233,.06);border:1px solid rgba(193,200,228,.4);color:var(--muted);align-self:flex-end;max-width:85%}
    .ai-bubble-bot{background:#fff;border:1px solid rgba(193,200,228,.5);align-self:flex-start;max-width:90%;box-shadow:var(--shadow-soft)}
    .ai-mock-input{padding:.75rem 1.25rem;border-top:1px solid rgba(193,200,228,.4);display:flex;gap:.5rem;align-items:center}
    .ai-mock-input input{flex:1;border:none;outline:none;font:inherit;font-size:.8rem;color:var(--muted);background:transparent}
    .ai-mock-input button{width:32px;height:32px;border-radius:.5rem;background:var(--primary);border:none;color:#fff;display:flex;align-items:center;justify-content:center;cursor:default}

    /* Testimonials */
    .testimonials-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:2.5rem}
    .testimonial-stars{color:#f59e0b;font-size:.85rem;letter-spacing:2px;margin-bottom:.75rem}
    .testimonial-text{font-size:.875rem;line-height:1.7;color:var(--fg)}
    .testimonial-author{margin-top:1.25rem;display:flex;align-items:center;gap:.75rem}
    .testimonial-avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#fff}
    .testimonial-name{font-size:.8rem;font-weight:600}
    .testimonial-role{font-size:.7rem;color:var(--muted)}

    /* CTA */
    .cta-section{position:relative;overflow:hidden;border-radius:var(--radius-lg);border:1px solid rgba(193,200,228,.5);background:#fff;box-shadow:var(--shadow-md);padding:3.5rem 2.5rem}
    .cta-overlay{position:absolute;inset:0;background:var(--gradient-hero);opacity:.08}

    /* Footer */
    .footer{border-top:1px solid rgba(193,200,228,.5);background:#fff;padding:3rem 0 2rem}
    .footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:3rem}
    .footer-brand-desc{font-size:.875rem;color:var(--muted);margin-top:.75rem;max-width:300px;line-height:1.7}
    .footer-col-title{font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:1rem}
    .footer-col ul{list-style:none}
    .footer-col li{margin-bottom:.5rem}
    .footer-col a{font-size:.875rem;color:var(--muted);transition:color .2s}
    .footer-col a:hover{color:var(--primary)}
    .footer-bottom{margin-top:2.5rem;padding-top:1.5rem;border-top:1px solid rgba(193,200,228,.4);display:flex;justify-content:space-between;align-items:center;font-size:.75rem;color:var(--muted)}
    .footer-socials{display:flex;gap:.75rem}
    .footer-social{width:34px;height:34px;border-radius:.6rem;background:var(--bg);border:1px solid rgba(193,200,228,.5);display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .2s;font-size:.8rem}
    .footer-social:hover{background:rgba(86,128,233,.08);color:var(--primary);border-color:rgba(86,128,233,.3)}

    /* Modal */
    .modal-backdrop{position:fixed;inset:0;z-index:60;background:rgba(0,0,0,.3);backdrop-filter:blur(4px);display:none}
    .modal-backdrop.open{display:flex;align-items:center;justify-content:center}
    .modal{width:100%;max-width:480px;margin:1rem;border-radius:var(--radius-lg);border:1px solid rgba(193,200,228,.5);background:#fff;box-shadow:var(--shadow-md);animation:scaleIn .2s ease-out}
    .modal-header{display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid rgba(193,200,228,.4)}
    .modal-body{padding:1.5rem}
    .modal-body label{display:block;font-size:.75rem;font-weight:600;color:var(--muted);margin-bottom:.35rem}
    .modal-body input,.modal-body textarea{width:100%;padding:.6rem .85rem;border:1px solid rgba(193,200,228,.6);border-radius:var(--radius);font:inherit;font-size:.875rem;outline:none;transition:border-color .2s}
    .modal-body input:focus,.modal-body textarea:focus{border-color:rgba(86,128,233,.5);box-shadow:0 0 0 3px rgba(86,128,233,.1)}
    .modal-field{margin-bottom:1rem}
    .field-error{font-size:.7rem;color:#dc2626;margin-top:.25rem;display:none}

    /* Toast */
    .toast-container{position:fixed;right:16px;bottom:16px;z-index:70;max-width:340px}
    .toast-item{background:#fff;border:1px solid rgba(193,200,228,.5);border-radius:var(--radius);padding:.75rem 1rem;box-shadow:var(--shadow-soft);display:flex;gap:.6rem;align-items:flex-start;margin-bottom:.5rem;animation:fadeUp .3s ease-out}
    .toast-dot{width:8px;height:8px;border-radius:50%;margin-top:6px;flex-shrink:0}

    /* Responsive */
    @media(min-width:768px){
      .hero-grid{grid-template-columns:1.05fr .95fr}
      .ai-grid{grid-template-columns:1fr 1fr}
    }
    /* Mobile menu panel */
    .mobile-menu{position:fixed;inset:0;z-index:55;display:none}
    .mobile-menu.open{display:block}
    .mobile-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.3);backdrop-filter:blur(4px)}
    .mobile-panel{position:absolute;top:0;right:0;width:280px;max-width:85vw;height:100%;background:#fff;box-shadow:-8px 0 30px rgba(0,0,0,.12);padding:1.5rem;display:flex;flex-direction:column;transform:translateX(100%);transition:transform .3s ease}
    .mobile-menu.open .mobile-panel{transform:translateX(0)}
    .mobile-close{align-self:flex-end;background:none;border:1px solid rgba(193,200,228,.5);border-radius:.5rem;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);margin-bottom:1.5rem}
    .mobile-close:hover{background:var(--bg)}
    .mobile-nav{display:flex;flex-direction:column;gap:.25rem}
    .mobile-nav a{display:block;padding:.75rem 1rem;border-radius:var(--radius);font-size:.95rem;font-weight:500;color:var(--fg);transition:all .2s}
    .mobile-nav a:hover{background:rgba(86,128,233,.06);color:var(--primary)}
    .mobile-nav-divider{height:1px;background:rgba(193,200,228,.4);margin:1rem 0}
    .mobile-nav .btn{width:100%;text-align:center;margin-top:.25rem}

    @media(max-width:1024px){
      .features-grid,.testimonials-grid{grid-template-columns:repeat(2,1fr)}
      .footer-grid{grid-template-columns:1fr 1fr}
    }
    @media(max-width:767px){
      .nav-links{display:none}
      .nav-ctas{display:none}
      .nav-mobile{display:flex}
      .container{padding:0 1rem}
      .hero{padding:2rem 0 2.5rem}
      .hero-grid{grid-template-columns:1fr;gap:2rem}
      .hero-title{font-size:1.75rem}
      .hero-subtitle{font-size:.9rem}
      .hero-ctas{flex-direction:column}
      .hero-ctas .btn{width:100%;justify-content:center}
      .hero-stats{grid-template-columns:repeat(3,1fr);gap:.5rem}
      .stat-card{padding:.75rem .6rem}
      .stat-value{font-size:1.1rem}
      .stat-label{font-size:.65rem}
      .section{padding:3rem 0}
      .section-title{font-size:1.35rem}
      .features-grid,.testimonials-grid{grid-template-columns:1fr}
      .ai-grid{grid-template-columns:1fr;gap:2rem}
      .footer-grid{grid-template-columns:1fr;gap:1.5rem}
      .footer-bottom{flex-direction:column;gap:.75rem;text-align:center}
      .cta-section{padding:2rem 1.25rem}
      .orb{display:none}
    }
  </style>
</head>
<body>
  <!-- NAV -->
  <header class="nav" id="nav">
    <div class="container nav-inner">
      <a href="{{ url('/') }}" class="nav-logo">
        <span class="nav-logo-icon">
          <img src="{{ asset('images/managex_logo.png') }}" alt="" style="width:28px;height:28px;border-radius:50%;object-fit:cover">
        </span>
        <span class="text-gradient">ManageX</span>
      </a>
      <nav class="nav-links">
        <a class="link-hover" href="#about">Plateforme</a>
        <a class="link-hover" href="#features">Fonctionnalités</a>
        <a class="link-hover" href="#ai">IA</a>
        <a class="link-hover" href="#testimonials">Témoignages</a>
      </nav>
      <div class="nav-ctas">
        <a href="{{ route('demo-request') }}" class="btn btn-outline" style="padding:.55rem 1.1rem">Demander une démo</a>
        <a href="{{ route('login') }}" class="btn btn-primary" style="padding:.55rem 1.1rem">Connexion</a>
      </div>
      <button class="nav-mobile" id="mobileToggle" aria-label="Menu">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>
  </header>

  <!-- MOBILE MENU -->
  <div class="mobile-menu" id="mobileMenu">
    <div class="mobile-backdrop" id="mobileBackdrop"></div>
    <div class="mobile-panel">
      <button class="mobile-close" id="mobileClose" aria-label="Fermer le menu">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
      <nav class="mobile-nav">
        <a href="#about">Plateforme</a>
        <a href="#features">Fonctionnalités</a>
        <a href="#ai">IA</a>
        <a href="#testimonials">Témoignages</a>
        <div class="mobile-nav-divider"></div>
        <a href="{{ route('demo-request') }}" class="btn btn-outline" style="padding:.65rem 1rem">Demander une démo</a>
        <a href="{{ route('login') }}" class="btn btn-primary" style="padding:.65rem 1rem">Connexion</a>
      </nav>
    </div>
  </div>

  <main>
    <!-- HERO -->
    <section class="hero" id="hero">
      <div class="orb orb-1" aria-hidden="true"></div>
      <div class="orb orb-2" aria-hidden="true"></div>
      <div class="orb orb-3" aria-hidden="true"></div>

      <div class="container">
        <div class="hero-grid">
          <div class="animate-fadeUp">
            
            <h1 class="hero-title">Le backoffice RH<br><span class="text-gradient">moderne</span> pour piloter votre entreprise.</h1>
            <p class="hero-subtitle">Présences, tâches, congés, fiches de paie, sondages et statistiques — une seule plateforme claire, fluide et premium.</p>
            <div class="hero-ctas">
              <a href="{{ route('demo-request') }}" class="btn btn-primary">
                <span>Demander une démo</span>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              </a>
              <a href="#features" class="btn btn-outline">Voir les modules</a>
            </div>
            <div class="hero-stats">
              <div class="stat-card animate-fadeUp delay-1"><p class="stat-label">Modules intégrés</p><p class="stat-value">25+</p></div>
              <div class="stat-card animate-fadeUp delay-2"><p class="stat-label">Disponibilité</p><p class="stat-value">99.9%</p></div>
              <div class="stat-card animate-fadeUp delay-3"><p class="stat-label">Mise en route</p><p class="stat-value">5 min</p></div>
            </div>
          </div>

          <div class="hero-visual animate-fadeUp delay-2">
            <div class="hero-visual-glow" aria-hidden="true"></div>
            <div style="border-radius:var(--radius-lg);overflow:hidden;border:1px solid rgba(193,200,228,.5);box-shadow:var(--shadow-md)">
              <img src="{{ asset('images/hero-managex.png') }}" alt="Professionnelle utilisant le dashboard ManageX" style="width:100%;height:auto;display:block" loading="eager">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ABOUT -->
    <section class="section" id="about">
      <div class="container">
        <div style="display:grid;gap:3rem;align-items:center" class="ai-grid">
          <div class="reveal-left">
            <span class="section-tag">La plateforme</span>
            <h2 class="section-title">Tout votre écosystème RH, <span class="text-gradient">unifié</span></h2>
            <p class="section-desc">ManageX centralise l'intégralité de vos processus RH dans une interface élégante et intuitive. Du pointage géolocalisé à la génération automatique des fiches de paie.</p>
          </div>
          <div class="reveal-right" style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
            <div class="stat-card reveal"><p class="stat-label">Temps admin réduit</p><p class="stat-value" style="color:#059669">-70%</p></div>
            <div class="stat-card reveal"><p class="stat-label">Conformité paie</p><p class="stat-value" style="color:var(--primary)">100%</p></div>
            <div class="stat-card reveal"><p class="stat-label">Assistant IA</p><p class="stat-value" style="color:var(--accent)">24/7</p></div>
            <div class="stat-card reveal"><p class="stat-label">Coût d'intégration</p><p class="stat-value" style="color:var(--secondary)">0 FCFA</p></div>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURES -->
    <section class="section" id="features" style="padding-top:2rem">
      <div class="container">
        <span class="section-tag reveal">Fonctionnalités</span>
        <h2 class="section-title reveal">Chaque module, pensé pour<br>l'<span class="text-gradient">excellence</span> RH</h2>
        <p class="section-desc reveal">Une suite complète qui couvre l'intégralité du cycle de vie employé.</p>
        <div class="features-grid">
          <div class="card reveal">
            <div class="feature-icon" style="background:rgba(86,128,233,.1);color:var(--primary)">
              <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
            <h3 class="feature-title">Pointage intelligent</h3>
            <p class="feature-desc">Check-in/out géolocalisé, zones GPS configurables, gestion automatique des retards et récupérations.</p>
          </div>
          <div class="card reveal">
            <div class="feature-icon" style="background:rgba(5,150,105,.1);color:#059669">
              <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <h3 class="feature-title">Gestion des congés</h3>
            <p class="feature-desc">Demandes en un clic, soldes automatiques, workflow d'approbation, calendrier d'équipe synchronisé.</p>
          </div>
          <div class="card reveal">
            <div class="feature-icon" style="background:rgba(245,158,11,.1);color:#d97706">
              <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 class="feature-title">Paie multi-pays</h3>
            <p class="feature-desc">Fiches de paie automatisées selon les barèmes locaux. Calculs CNPS, IRPP, primes et retenues.</p>
          </div>
          <div class="card reveal">
            <div class="feature-icon" style="background:rgba(136,96,208,.1);color:var(--accent)">
              <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>
            </div>
            <h3 class="feature-title">Analytics temps réel</h3>
            <p class="feature-desc">Dashboard interactif, KPIs, graphiques de tendances, taux de présence et export PDF/Excel.</p>
          </div>
          <div class="card reveal">
            <div class="feature-icon" style="background:rgba(239,68,68,.08);color:#dc2626">
              <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
            <h3 class="feature-title">Tâches & Kanban</h3>
            <p class="feature-desc">Assignation, suivi de progression, vues liste/Kanban/calendrier et notifications.</p>
          </div>
          <div class="card reveal">
            <div class="feature-icon" style="background:rgba(90,185,234,.12);color:var(--secondary)">
              <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <h3 class="feature-title">Messagerie interne</h3>
            <p class="feature-desc">Conversations privées et groupes, images, vocaux, pièces jointes. Temps réel via WebSocket.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- AI -->
    <section class="ai-section section" id="ai">
      <div class="container">
        <div class="ai-grid">
          <div class="reveal-left">
            <span class="section-tag">Intelligence artificielle</span>
            <h2 class="section-title">Un assistant IA qui comprend <span class="text-gradient">vos données RH</span></h2>
            <p class="section-desc">ManageX intègre Mistral AI pour transformer vos données en insights actionnables.</p>
            <ul class="ai-features">
              <li class="ai-feature"><span class="ai-feature-icon">✦</span><span>Chatbot RH pour les employés (congés, retards, tâches)</span></li>
              <li class="ai-feature"><span class="ai-feature-icon">✦</span><span>Résumés analytics automatiques pour l'admin</span></li>
              <li class="ai-feature"><span class="ai-feature-icon">✦</span><span>Contexte temps réel : données entreprise injectées</span></li>
              <li class="ai-feature"><span class="ai-feature-icon">✦</span><span>Sécurisé : aucune donnée sensible transmise</span></li>
            </ul>
          </div>

          <!-- Chat Widget -->
          <div class="reveal-right" style="display:flex;justify-content:center">
            <div style="width:340px;border-radius:1.25rem;overflow:hidden;box-shadow:0 25px 60px -12px rgba(86,128,233,.25),0 0 0 1px rgba(193,200,228,.4);background:#fff">
              <!-- Header gradient -->
              <div style="background:linear-gradient(135deg,#5680E9 0%,#84CEEB 100%);padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between">
                <div style="display:flex;align-items:center;gap:.65rem">
                  <div style="width:36px;height:36px;border-radius:.65rem;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center">
                    <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a3 3 0 00-3 3v1a3 3 0 006 0V5a3 3 0 00-3-3zM19 10H5a1 1 0 00-1 1v1a8 8 0 0016 0v-1a1 1 0 00-1-1z"/></svg>
                  </div>
                  <div>
                    <p style="font-size:.875rem;font-weight:700;color:#fff">Assistant RH</p>
                    <p style="font-size:.65rem;color:rgba(255,255,255,.8)">Propulsé par Mistral AI</p>
                  </div>
                </div>
                <div style="width:28px;height:28px;border-radius:.5rem;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center">
                  <svg width="14" height="14" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </div>
              </div>
              <!-- Body -->
              <div style="padding:1.75rem 1.25rem;text-align:center">
                <!-- Chat icon -->
                <div style="width:52px;height:52px;border-radius:50%;background:rgba(86,128,233,.08);border:2px solid rgba(86,128,233,.15);margin:0 auto .75rem;display:flex;align-items:center;justify-content:center">
                  <svg width="24" height="24" fill="none" stroke="var(--primary)" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                </div>
                <p style="font-size:1rem;font-weight:700;margin-bottom:.2rem">Bonjour MELVIN !</p>
                <p style="font-size:.8rem;color:var(--muted)">Comment puis-je vous aider ?</p>

                <!-- Suggestion chips -->
                <div style="display:flex;flex-direction:column;gap:.5rem;margin-top:1.25rem">
                  <div style="padding:.7rem 1rem;border:1px solid rgba(193,200,228,.5);border-radius:var(--radius);font-size:.8rem;color:var(--muted);text-align:left;cursor:pointer;transition:all .2s;background:#fff" onmouseover="this.style.borderColor='rgba(86,128,233,.4)';this.style.background='rgba(86,128,233,.03)'" onmouseout="this.style.borderColor='rgba(193,200,228,.5)';this.style.background='#fff'">
                    Combien de jours de congé me reste-t-il ?
                  </div>
                  <div style="padding:.7rem 1rem;border:1px solid rgba(193,200,228,.5);border-radius:var(--radius);font-size:.8rem;color:var(--muted);text-align:left;cursor:pointer;transition:all .2s;background:#fff" onmouseover="this.style.borderColor='rgba(86,128,233,.4)';this.style.background='rgba(86,128,233,.03)'" onmouseout="this.style.borderColor='rgba(193,200,228,.5)';this.style.background='#fff'">
                    Quel est mon solde de retard ce mois ?
                  </div>
                  <div style="padding:.7rem 1rem;border:1px solid rgba(193,200,228,.5);border-radius:var(--radius);font-size:.8rem;color:var(--muted);text-align:left;cursor:pointer;transition:all .2s;background:#fff" onmouseover="this.style.borderColor='rgba(86,128,233,.4)';this.style.background='rgba(86,128,233,.03)'" onmouseout="this.style.borderColor='rgba(193,200,228,.5)';this.style.background='#fff'">
                    Comment faire une demande de congé ?
                  </div>
                </div>
              </div>
              <!-- Input -->
              <div style="padding:.75rem 1rem;border-top:1px solid rgba(193,200,228,.35);display:flex;align-items:center;gap:.5rem">
                <input type="text" placeholder="Posez votre question..." disabled style="flex:1;border:1.5px solid rgba(86,128,233,.25);border-radius:.6rem;padding:.6rem .85rem;font:inherit;font-size:.8rem;color:var(--muted);outline:none;background:#fff">
                <button disabled aria-label="Envoyer un message" style="width:38px;height:38px;border-radius:.6rem;background:var(--primary);border:none;color:#fff;display:flex;align-items:center;justify-content:center;cursor:default;box-shadow:0 4px 12px rgba(86,128,233,.3)">
                  <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="section" id="testimonials">
      <div class="container">
        <span class="section-tag">Témoignages</span>
        <h2 class="section-title">Ce qu'en disent nos <span class="text-gradient">utilisateurs</span></h2>
        <div class="testimonials-grid">
          <div class="card reveal">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"ManageX a transformé notre gestion RH. Le pointage géolocalisé a éliminé les fraudes et l'assistant IA nous fait gagner 2h par jour."</p>
            <div class="testimonial-author">
              <div class="testimonial-avatar" style="background:var(--primary)">AK</div>
              <div><div class="testimonial-name">Aminata Koné</div><div class="testimonial-role">DRH — Groupe Solaris</div></div>
            </div>
          </div>
          <div class="card reveal">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"La paie multi-pays est un game changer. Nous opérons en Côte d'Ivoire et au Sénégal, les fiches sont générées automatiquement."</p>
            <div class="testimonial-author">
              <div class="testimonial-avatar" style="background:var(--accent)">MD</div>
              <div><div class="testimonial-name">Marc Diallo</div><div class="testimonial-role">CEO — TechBridge Africa</div></div>
            </div>
          </div>
          <div class="card reveal">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"En tant qu'employée, j'adore la simplicité. Je pointe en un clic, je vois mes congés, et le chatbot IA répond instantanément."</p>
            <div class="testimonial-author">
              <div class="testimonial-avatar" style="background:var(--secondary)">FB</div>
              <div><div class="testimonial-name">Fatou Bamba</div><div class="testimonial-role">Développeuse — NovaTech</div></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="section" id="contact">
      <div class="container">
        <div class="cta-section reveal-scale">
          <div class="cta-overlay" aria-hidden="true"></div>
          <div class="orb" style="width:250px;height:250px;top:-80px;right:-60px;background:rgba(90,185,234,.15);filter:blur(50px)" aria-hidden="true"></div>
          <div class="orb" style="width:200px;height:200px;bottom:-60px;left:-40px;background:rgba(136,96,208,.12);filter:blur(50px)" aria-hidden="true"></div>
          <div style="position:relative;display:grid;gap:2rem;align-items:center" class="ai-grid">
            <div>
              <h2 class="section-title">Modernisez votre gestion RH <span class="text-gradient">dès aujourd'hui</span></h2>
              <p class="section-desc">Rejoignez les entreprises qui ont choisi ManageX pour simplifier et piloter leur capital humain.</p>
            </div>
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;justify-content:flex-end">
              <a href="{{ route('demo-request') }}" class="btn btn-outline">Demander une démo</a>
              <a href="{{ route('login') }}" class="btn btn-primary">Se connecter</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div>
         " <a href="{{ url('/') }}" class="nav-logo"><span class="text-gradient" style="font-size:1.1rem">ManageX</span></a>"
          <p class="footer-brand-desc">La plateforme RH nouvelle génération propulsée par l'IA. Conçue pour les entreprises ambitieuses d'Afrique et du monde.</p>
        </div>
        <div class="footer-col">
          <h3 class="footer-col-title">Produit</h3>
          <ul><li><a href="#features" class="link-hover">Fonctionnalités</a></li><li><a href="#ai" class="link-hover">Intelligence IA</a></li><li><a href="{{ route('demo-request') }}" class="link-hover">Démo</a></li></ul>
        </div>
        <div class="footer-col">
          <h3 class="footer-col-title">Entreprise</h3>
          <ul><li><a href="#about" class="link-hover">À propos</a></li><li><a href="#contact" class="link-hover">Contact</a></li><li><a href="#" class="link-hover">Confidentialité</a></li></ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} ManageX. Tous droits réservés.</span>
        <div class="footer-socials">
          <a href="#" class="footer-social" aria-label="LinkedIn"><svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6zM2 9h4v12H2zM4 2a2 2 0 110 4 2 2 0 010-4z"/></svg></a>
          <a href="#" class="footer-social" aria-label="Twitter/X"><svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
          <a href="#" class="footer-social" aria-label="GitHub"><svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg></a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Toast container -->
  <div id="toastRoot" class="toast-container" aria-live="polite"></div>

  <script>
    // Mobile menu
    const mobileMenu=document.getElementById('mobileMenu');
    const mobileToggle=document.getElementById('mobileToggle');
    const mobileClose=document.getElementById('mobileClose');
    const mobileBackdrop=document.getElementById('mobileBackdrop');
    function openMobile(){mobileMenu.classList.add('open');document.body.style.overflow='hidden'}
    function closeMobile(){mobileMenu.classList.remove('open');document.body.style.overflow=''}
    mobileToggle.addEventListener('click',openMobile);
    mobileClose.addEventListener('click',closeMobile);
    mobileBackdrop.addEventListener('click',closeMobile);
    mobileMenu.querySelectorAll('a').forEach(a=>a.addEventListener('click',closeMobile));

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(a=>{
      a.addEventListener('click',e=>{e.preventDefault();const t=document.querySelector(a.getAttribute('href'));if(t)t.scrollIntoView({behavior:'smooth',block:'start'})})
    });

    // Hero chart bars
    const chartEl=document.getElementById('heroChart');
    if(chartEl){
      const heights=[45,60,55,70,65,80,75,85,90,70,60,80,95,75,85,90,65,70,80,85,60,75,90,70,80,85,95,70,75,80];
      heights.forEach((h,i)=>{const b=document.createElement('div');b.className='hero-chart-bar';b.style.height='6px';b.style.transitionDelay=i*25+'ms';chartEl.appendChild(b)});
      const obs=new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){e.target.querySelectorAll('.hero-chart-bar').forEach((b,i)=>{setTimeout(()=>{b.style.height=heights[i]+'%'},150)});obs.unobserve(e.target)}})},{threshold:.3});
      obs.observe(chartEl);
    }

    // ✨ Magic scroll reveal
    const allRevealEls = document.querySelectorAll('.reveal,.reveal-left,.reveal-right,.reveal-scale');

    const revealObs = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          revealObs.unobserve(e.target);
        }
      });
    }, { threshold: 0.01, rootMargin: '0px 0px 50px 0px' });

    // Stagger children inside grids BEFORE observing
    document.querySelectorAll('.features-grid,.testimonials-grid,.hero-stats').forEach(grid => {
      grid.querySelectorAll('.reveal').forEach((child, i) => {
        child.style.transitionDelay = (i * 0.12) + 's';
      });
    });

    // Observe all reveal elements
    allRevealEls.forEach(el => revealObs.observe(el));

    // Fallback: reveal elements already in viewport on page load
    requestAnimationFrame(() => {
      allRevealEls.forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) {
          el.classList.add('visible');
          revealObs.unobserve(el);
        }
      });
    });

    // PWA Service Worker
    if('serviceWorker' in navigator){navigator.serviceWorker.register('{{ asset("sw.js") }}').catch(()=>{})}
  </script>
</body>
</html>
