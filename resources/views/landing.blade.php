<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>ManageX — Gestion RH intelligente</title>
  <meta name="description" content="ManageX centralise les présences, tâches, congés, paie, sondages et statistiques dans un seul tableau de bord clair et moderne." />
  <meta name="theme-color" content="#1B3C35">
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

  <!-- Canonical -->
  <link rel="canonical" href="{{ url('/') }}" />

  <!-- OpenGraph -->
  <meta property="og:type" content="website" />
  <meta property="og:title" content="ManageX — Gestion RH intelligente" />
  <meta property="og:description" content="ManageX centralise les présences, tâches, congés, paie, sondages et statistiques dans un seul tableau de bord clair et moderne." />
  <meta property="og:url" content="{{ url('/') }}" />
  <meta property="og:site_name" content="ManageX" />
  <meta property="og:image" content="{{ asset('images/hero-managex.png') }}" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:locale" content="fr_FR" />

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="ManageX — Gestion RH intelligente" />
  <meta name="twitter:description" content="ManageX centralise les présences, tâches, congés, paie, sondages et statistiques." />
  <meta name="twitter:image" content="{{ asset('images/hero-managex.png') }}" />

  <!-- Schema.org -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "ManageX",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "description": "ManageX centralise les présences, tâches, congés, paie, sondages et statistiques dans un seul tableau de bord clair et moderne.",
    "url": "{{ url('/') }}",
    "image": "{{ asset('images/hero-managex.png') }}",
    "author": {
      "@type": "Organization",
      "name": "YA Consulting",
      "url": "https://ya-consulting.com"
    },
    "offers": {
      "@type": "Offer",
      "price": "0",
      "priceCurrency": "XOF",
      "description": "Demandez une démo gratuite"
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "5",
      "reviewCount": "3",
      "bestRating": "5"
    }
  }
  </script>
  <link rel="manifest" href="{{ route('manifest') }}">
  <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=DM+Serif+Display&display=swap" rel="stylesheet">

  <style>
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
    :root{
      --green:#1B3C35;--green-light:#2D5A4E;--green-accent:#3D7A6A;
      --cream:#F5F0E8;--cream-light:#FAF7F2;--cream-dark:#ECE5D8;
      --gold:#C8A96E;--gold-light:#D4BC8B;
      --fg:#1B3C35;--muted:#5C6E68;--white:#fff;
      --radius:0.75rem;--radius-lg:1.25rem;--radius-xl:1.75rem;
      --shadow-soft:0 1px 3px rgba(27,60,53,.08);
      --shadow-md:0 8px 30px rgba(27,60,53,.1);
      --shadow-lg:0 20px 60px rgba(27,60,53,.12);
    }
    html{scroll-behavior:smooth;font-size:16px;-webkit-font-smoothing:antialiased}
    body{font-family:'DM Sans',system-ui,sans-serif;background:var(--cream);color:var(--fg);line-height:1.6;overflow-x:hidden}
    a{text-decoration:none;color:inherit}
    .container{max-width:1200px;margin:0 auto;padding:0 1.5rem}
    h1,h2,h3,.serif{font-family:'DM Serif Display',Georgia,serif}

    /* Animations */
    @keyframes fadeUp{0%{opacity:0;transform:translateY(24px)}100%{opacity:1;transform:translateY(0)}}
    @keyframes fadeIn{0%{opacity:0}100%{opacity:1}}
    @keyframes scaleIn{0%{opacity:0;transform:scale(.96)}100%{opacity:1;transform:scale(1)}}
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
    @keyframes slideLogos{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
    .animate-fadeUp{animation:fadeUp .6s ease-out both}
    .animate-fadeIn{animation:fadeIn .6s ease-out both}
    .delay-1{animation-delay:.1s}.delay-2{animation-delay:.2s}.delay-3{animation-delay:.3s}.delay-4{animation-delay:.4s}

    /* Scroll reveal */
    .reveal{opacity:0;transform:translateY(30px);transition:all .7s cubic-bezier(.16,1,.3,1)}
    .reveal.visible{opacity:1;transform:translateY(0)}
    @media(prefers-reduced-motion:reduce){.reveal{opacity:1;transform:none;transition:none}}

    /* ─── NAV ─── */
    .nav{position:sticky;top:0;z-index:50;background:var(--cream);border-bottom:1px solid var(--cream-dark);transition:all .3s}
    .nav.scrolled{background:rgba(245,240,232,.92);backdrop-filter:blur(16px);box-shadow:var(--shadow-soft)}
    .nav-inner{display:flex;align-items:center;justify-content:space-between;height:110px}
    .nav-logo{display:inline-flex;align-items:center;gap:.6rem;font-size:1.05rem;font-weight:700;color:var(--green)}
    .nav-logo img{height:80px;width:auto;object-fit:contain}
    .nav-links{display:flex;align-items:center;gap:2.25rem;font-size:.875rem;font-weight:500;color:var(--muted)}
    .nav-links a{transition:color .2s}.nav-links a:hover{color:var(--green)}
    .nav-ctas{display:flex;align-items:center;gap:.6rem}
    .nav-mobile{display:none;background:none;border:none;cursor:pointer;padding:.5rem;color:var(--green)}

    /* ─── BUTTONS ─── */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.7rem 1.5rem;border-radius:999px;font-size:.875rem;font-weight:600;border:none;cursor:pointer;transition:all .25s;font-family:inherit}
    .btn:hover{transform:translateY(-1px)}
    .btn-primary{background:var(--green);color:var(--cream);box-shadow:0 2px 8px rgba(27,60,53,.2)}
    .btn-primary:hover{background:var(--green-light);box-shadow:0 4px 16px rgba(27,60,53,.25)}
    .btn-outline{background:transparent;border:1.5px solid var(--cream-dark);color:var(--green)}
    .btn-outline:hover{border-color:var(--green);background:rgba(27,60,53,.04)}
    .btn-gold{background:var(--gold);color:var(--green);box-shadow:0 2px 8px rgba(200,169,110,.3)}
    .btn-gold:hover{background:var(--gold-light)}

    /* ─── HERO ─── */
    .hero{padding:5rem 0 4rem;text-align:center;position:relative}
    .hero-badge{display:inline-flex;align-items:center;gap:.5rem;padding:.4rem 1rem .4rem .6rem;border-radius:999px;background:var(--white);border:1px solid var(--cream-dark);font-size:.8rem;color:var(--muted);margin-bottom:2rem;box-shadow:var(--shadow-soft)}
    .hero-badge-icon{width:24px;height:24px;border-radius:50%;background:var(--green);display:flex;align-items:center;justify-content:center}
    .hero-badge-icon svg{width:12px;height:12px;stroke:#fff;fill:none;stroke-width:2.5}
    .hero-title{font-size:clamp(2.2rem,5.5vw,3.8rem);line-height:1.1;letter-spacing:-.02em;color:var(--green);max-width:800px;margin:0 auto}
    .hero-title em{font-style:italic;color:var(--green-accent)}
    .hero-subtitle{font-size:clamp(.95rem,1.5vw,1.1rem);color:var(--muted);max-width:560px;margin:1.5rem auto 0;line-height:1.7}
    .hero-ctas{display:flex;gap:.75rem;justify-content:center;margin-top:2.5rem;flex-wrap:wrap}
    .hero-avatars{display:flex;align-items:center;justify-content:center;gap:-.5rem;margin-top:2.5rem}
    .hero-avatars-stack{display:flex}
    .hero-avatar{width:36px;height:36px;border-radius:50%;border:2.5px solid var(--cream);margin-left:-10px;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;color:#fff}
    .hero-avatar:first-child{margin-left:0}
    .hero-avatars-text{font-size:.8rem;color:var(--muted);margin-left:.75rem}
    .hero-avatars-text strong{color:var(--green);font-weight:600}

    /* ─── LOGOS BAND ─── */
    .logos-section{padding:3rem 0;border-top:1px solid var(--cream-dark);border-bottom:1px solid var(--cream-dark);background:var(--cream-light);overflow:hidden}
    .logos-label{text-align:center;font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);margin-bottom:1.5rem}
    .logos-track{display:flex;align-items:center;gap:3rem;animation:slideLogos 20s linear infinite;width:max-content}
    .logos-track span{font-size:1.1rem;font-weight:700;color:var(--muted);opacity:.5;white-space:nowrap;letter-spacing:.02em}

    /* ─── SECTION COMMONS ─── */
    .section{padding:5rem 0}
    .section-tag{font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.12em;color:var(--green-accent);margin-bottom:.75rem;display:block}
    .section-title{font-size:clamp(1.6rem,3.5vw,2.5rem);line-height:1.15;letter-spacing:-.01em;color:var(--green)}
    .section-desc{font-size:.95rem;color:var(--muted);margin-top:.75rem;max-width:540px;line-height:1.7}

    /* ─── BENTO FEATURES ─── */
    .bento-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-top:3rem}
    .bento-card{background:var(--white);border:1px solid var(--cream-dark);border-radius:var(--radius-lg);padding:2rem;transition:all .35s;position:relative;overflow:hidden}
    .bento-card:hover{box-shadow:var(--shadow-md);border-color:rgba(27,60,53,.15);transform:translateY(-3px)}
    .bento-card-wide{grid-column:span 2}
    .bento-card-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;flex-shrink:0}
    .bento-card-icon svg{width:24px;height:24px;stroke-width:1.8}
    .bento-card-title{font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--green);margin-bottom:.5rem}
    .bento-card-desc{font-size:.85rem;color:var(--muted);line-height:1.65;max-width:480px}
    .bento-card-visual{margin-top:1.5rem}

    /* Mini chart */
    .mini-chart{display:flex;align-items:flex-end;gap:4px;height:70px}
    .mini-bar{flex:1;border-radius:4px 4px 0 0;background:var(--green);opacity:.6;transition:height .8s cubic-bezier(.16,1,.3,1)}
    .mini-bar:nth-child(even){background:var(--gold);opacity:.7}

    /* Feature highlights list */
    .feat-highlights{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:1rem}
    .feat-chip{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .75rem;border-radius:999px;font-size:.72rem;font-weight:500;background:var(--cream-light);border:1px solid var(--cream-dark);color:var(--muted)}
    .feat-chip svg{width:12px;height:12px;stroke:var(--green-accent);fill:none;stroke-width:2.5}

    /* ─── INTEGRATIONS ─── */
    .integrations-section{background:var(--green);color:var(--cream);padding:4rem 0;text-align:center;position:relative;overflow:hidden}
    .integrations-section .section-title{color:var(--cream)}
    .integrations-section .section-desc{color:rgba(245,240,232,.7);margin-left:auto;margin-right:auto}
    .integrations-grid{display:flex;flex-wrap:wrap;justify-content:center;gap:1rem;margin-top:2.5rem}
    .integration-icon{width:56px;height:56px;border-radius:var(--radius);background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;transition:all .3s;font-size:1.5rem}
    .integration-icon:hover{background:rgba(255,255,255,.18);transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.2)}

    /* ─── TESTIMONIAL ─── */
    .testimonial-section{padding:5rem 0;text-align:center}
    .testimonial-quote-mark{font-size:4rem;color:var(--green);line-height:1;font-family:'DM Serif Display',serif;opacity:.3}
    .testimonial-text{font-size:clamp(1rem,2vw,1.3rem);max-width:700px;margin:1rem auto;line-height:1.7;color:var(--green);font-style:italic}
    .testimonial-author-card{display:inline-flex;align-items:center;gap:.75rem;margin-top:1.5rem}
    .testimonial-author-avatar{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#fff}
    .testimonial-author-info{text-align:left}
    .testimonial-author-name{font-size:.875rem;font-weight:600;color:var(--green)}
    .testimonial-author-role{font-size:.75rem;color:var(--muted)}
    .testimonial-stars{color:var(--gold);font-size:1rem;letter-spacing:2px;margin-bottom:.5rem}

    /* ─── STATS ─── */
    .stats-section{padding:4rem 0;border-top:1px solid var(--cream-dark)}
    .stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;text-align:center}
    .stat-number{font-family:'DM Serif Display',serif;font-size:clamp(2rem,4vw,3.2rem);color:var(--green)}
    .stat-label{font-size:.85rem;color:var(--muted);margin-top:.35rem}

    /* ─── CTA ─── */
    .cta-section{background:var(--green);border-radius:var(--radius-xl);padding:3.5rem 2.5rem;text-align:center;position:relative;overflow:hidden}
    .cta-section .section-title{color:var(--cream)}
    .cta-section .section-desc{color:rgba(245,240,232,.7);margin-left:auto;margin-right:auto}
    .cta-orb{position:absolute;border-radius:50%;filter:blur(80px);pointer-events:none}

    /* ─── FOOTER ─── */
    .footer{background:var(--green);color:rgba(245,240,232,.7);padding:4rem 0 2rem;margin-top:5rem}
    .footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:3rem}
    .footer-logo{color:var(--cream);font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.5rem}
    .footer-logo img{height:80px;width:auto;object-fit:contain}
    .footer-desc{font-size:.85rem;margin-top:.75rem;max-width:280px;line-height:1.7;color:rgba(245,240,232,.5)}
    .footer-col-title{font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--cream);margin-bottom:1rem}
    .footer-col ul{list-style:none}
    .footer-col li{margin-bottom:.5rem}
    .footer-col a{font-size:.85rem;transition:color .2s;color:rgba(245,240,232,.5)}
    .footer-col a:hover{color:var(--cream)}
    .footer-bottom{margin-top:3rem;padding-top:1.5rem;border-top:1px solid rgba(245,240,232,.1);display:flex;justify-content:space-between;align-items:center;font-size:.75rem;color:rgba(245,240,232,.35)}
    .footer-socials{display:flex;gap:.6rem}
    .footer-social{width:36px;height:36px;border-radius:50%;background:rgba(245,240,232,.08);display:flex;align-items:center;justify-content:center;color:rgba(245,240,232,.4);transition:all .2s}
    .footer-social:hover{background:rgba(245,240,232,.15);color:var(--cream)}

    /* ─── MOBILE MENU ─── */
    .mobile-menu{position:fixed;inset:0;z-index:55;display:none}
    .mobile-menu.open{display:block}
    .mobile-backdrop{position:absolute;inset:0;background:rgba(27,60,53,.4);backdrop-filter:blur(4px)}
    .mobile-panel{position:absolute;top:0;right:0;width:300px;max-width:85vw;height:100%;background:var(--cream);box-shadow:-8px 0 30px rgba(0,0,0,.12);padding:1.5rem;display:flex;flex-direction:column;transform:translateX(100%);transition:transform .3s ease}
    .mobile-menu.open .mobile-panel{transform:translateX(0)}
    .mobile-close{align-self:flex-end;background:none;border:1px solid var(--cream-dark);border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);margin-bottom:2rem}
    .mobile-close:hover{background:var(--cream-dark)}
    .mobile-nav{display:flex;flex-direction:column;gap:.25rem}
    .mobile-nav a{display:block;padding:.75rem 1rem;border-radius:var(--radius);font-size:.95rem;font-weight:500;color:var(--green);transition:all .2s}
    .mobile-nav a:hover{background:rgba(27,60,53,.06)}
    .mobile-nav-divider{height:1px;background:var(--cream-dark);margin:1rem 0}
    .mobile-nav .btn{width:100%;text-align:center;margin-top:.25rem}

    /* ─── RESPONSIVE ─── */
    @media(max-width:1024px){
      .bento-grid{grid-template-columns:repeat(2,1fr)}
      .bento-card-wide{grid-column:span 2}
      .stats-grid{grid-template-columns:repeat(3,1fr)}
      .footer-grid{grid-template-columns:1fr 1fr}
    }
    @media(max-width:767px){
      .nav-links,.nav-ctas{display:none}
      .nav-mobile{display:flex}
      .container{padding:0 1rem}
      .hero{padding:3rem 0 2rem}
      .hero-title{font-size:2rem}
      .hero-ctas{flex-direction:column;align-items:center}
      .hero-ctas .btn{width:100%;max-width:300px}
      .section{padding:3rem 0}
      .bento-grid{grid-template-columns:1fr}
      .bento-card-wide{grid-column:span 1}
      .stats-grid{grid-template-columns:1fr;gap:1.5rem}
      .footer-grid{grid-template-columns:1fr;gap:1.5rem}
      .footer-bottom{flex-direction:column;gap:.75rem;text-align:center}
      .integrations-grid{gap:.6rem}
      .integration-icon{width:44px;height:44px;font-size:1.2rem}
    }
  </style>
</head>
<body>
  <!-- ═══ NAV ═══ -->
  <header class="nav" id="nav">
    <div class="container nav-inner">
      <a href="{{ url('/') }}" class="nav-logo">
        <img src="{{ asset('images/managex_logo.png') }}" alt="ManageX Logo">
      </a>
      <nav class="nav-links">
        <a href="#features">Fonctionnalités</a>
        <a href="#integrations">Intégrations</a>
        <a href="#testimonials">Témoignages</a>
        <a href="#contact">Contact</a>
      </nav>
      <div class="nav-ctas">
        <a href="{{ route('demo-request') }}" class="btn btn-outline" style="padding:.55rem 1.25rem">Demander une démo</a>
        <a href="{{ route('login') }}" class="btn btn-primary" style="padding:.55rem 1.25rem">Connexion</a>
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
        <a href="#features">Fonctionnalités</a>
        <a href="#integrations">Intégrations</a>
        <a href="#testimonials">Témoignages</a>
        <a href="#contact">Contact</a>
        <div class="mobile-nav-divider"></div>
        <a href="{{ route('demo-request') }}" class="btn btn-outline">Demander une démo</a>
        <a href="{{ route('login') }}" class="btn btn-primary">Connexion</a>
      </nav>
    </div>
  </div>

  <main>
    <!-- ═══ HERO ═══ -->
    <section class="hero" id="hero">
      <div class="container">
        

        <h1 class="hero-title animate-fadeUp delay-1">
          Un seul outil pour <em>gérer</em><br>vos équipes et votre entreprise
        </h1>

        <p class="hero-subtitle animate-fadeUp delay-2">
          ManageX aide les équipes RH à travailler plus vite et plus efficacement, en centralisant la visibilité et les insights basés sur les données dans une plateforme intuitive.
        </p>

        <div class="hero-ctas animate-fadeUp delay-3">
          <a href="{{ route('demo-request') }}" class="btn btn-primary" style="padding:.85rem 2rem;font-size:.95rem">
            <span>Essayer gratuitement</span>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
          <a href="#features" class="btn btn-outline" style="padding:.85rem 2rem;font-size:.95rem">Voir les modules</a>
        </div>

        <div class="hero-avatars animate-fadeUp delay-4">
          <div class="hero-avatars-stack">
            <div class="hero-avatar" style="background:var(--green)">AK</div>
            <div class="hero-avatar" style="background:var(--green-accent)">MD</div>
            <div class="hero-avatar" style="background:var(--gold)">FB</div>
            <div class="hero-avatar" style="background:var(--green-light)">KT</div>
          </div>
          <span class="hero-avatars-text">Rejoint par <strong>150+ entreprises</strong> en Afrique</span>
        </div>
      </div>
    </section>

    <!-- ═══ LOGOS BAND ═══ -->
    <section class="logos-section">
      <p class="logos-label">Ils nous font confiance</p>
      <div style="overflow:hidden">
        <div class="logos-track">
          <span>YA Consulting</span><span>•</span>
          <span>Groupe Solaris</span><span>•</span>
          <span>TechBridge Africa</span><span>•</span>
          <span>NovaTech</span><span>•</span>
          <span>Abidjan Digital</span><span>•</span>
          <span>Invest Corp</span><span>•</span>
          <span>YA Consulting</span><span>•</span>
          <span>Groupe Solaris</span><span>•</span>
          <span>TechBridge Africa</span><span>•</span>
          <span>NovaTech</span><span>•</span>
          <span>Abidjan Digital</span><span>•</span>
          <span>Invest Corp</span>
        </div>
      </div>
    </section>

    <!-- ═══ FEATURES BENTO ═══ -->
    <section class="section" id="features">
      <div class="container">
        <div style="text-align:center;margin-bottom:1rem">
          <span class="section-tag reveal">Fonctionnalités</span>
          <h2 class="section-title reveal" style="max-width:650px;margin:0 auto">Tout ce dont vous avez besoin, <em style="font-style:italic;color:var(--gold)">unifié.</em></h2>
          <p class="section-desc reveal" style="margin-left:auto;margin-right:auto">ManageX centralise votre écosystème RH dans une seule plateforme intuitive et abordable.</p>
        </div>

        <div class="bento-grid">
          <!-- ── ROW 1 ── -->
          <!-- Dashboard (wide) -->
          <div class="bento-card bento-card-wide reveal">
            <div style="display:flex;gap:1.5rem;align-items:flex-start">
              <div class="bento-card-icon" style="background:rgba(27,60,53,.08)">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--green)"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="4" rx="1"/><rect x="14" y="10" width="7" height="7" rx="1" opacity=".5"/><rect x="3" y="13" width="7" height="4" rx="1" opacity=".5"/></svg>
              </div>
              <div style="flex:1">
                <h3 class="bento-card-title">Tableau de bord dynamique</h3>
                <p class="bento-card-desc">Visualisez les KPIs en temps réel : taux de présence, tâches en cours, congés validés. Tout en un coup d'œil.</p>
                <div class="bento-card-visual">
                  <div style="display:flex;align-items:center;gap:1rem;margin-bottom:.75rem">
                    <div style="display:flex;align-items:center;gap:.35rem"><div style="width:28px;height:28px;border-radius:50%;background:var(--green);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.5rem;font-weight:700">92%</div><span style="font-size:.7rem;color:var(--muted)">Présences</span></div>
                    <div style="display:flex;align-items:center;gap:.35rem"><div style="width:28px;height:28px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.5rem;font-weight:700">+5</div><span style="font-size:.7rem;color:var(--muted)">Nouvelles</span></div>
                  </div>
                  <div class="mini-chart" id="heroChart"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Notifications -->
          <div class="bento-card reveal">
            <div class="bento-card-icon" style="background:rgba(200,169,110,.12)">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold)"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
            </div>
            <h3 class="bento-card-title">Notifications intelligentes</h3>
            <p class="bento-card-desc">Alertes en temps réel : retards, congés, tâches, évaluations. Push, email et in-app.</p>
            <div class="feat-highlights">
              <span class="feat-chip"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Push</span>
              <span class="feat-chip"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Email</span>
              <span class="feat-chip"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>In-app</span>
            </div>
          </div>

          <!-- ── ROW 2 ── -->
          <!-- Pointage -->
          <div class="bento-card reveal">
            <div class="bento-card-icon" style="background:rgba(61,122,106,.1)">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--green-accent)"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3 class="bento-card-title">Pointage géolocalisé</h3>
            <p class="bento-card-desc">Check-in/out avec zones GPS configurables. Détection automatique des retards et heures supplémentaires.</p>
            <div class="feat-highlights">
              <span class="feat-chip"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>GPS</span>
              <span class="feat-chip"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Auto-checkout</span>
            </div>
          </div>

          <!-- Tâches -->
          <div class="bento-card reveal">
            <div class="bento-card-icon" style="background:rgba(27,60,53,.08)">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--green)"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
            <h3 class="bento-card-title">Gestion des tâches</h3>
            <p class="bento-card-desc">Assignez, suivez et validez les tâches avec des vues Kanban, calendrier et listes.</p>
            <div class="feat-highlights">
              <span class="feat-chip"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Kanban</span>
              <span class="feat-chip"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Calendrier</span>
            </div>
          </div>

          <!-- Congés -->
          <div class="bento-card reveal">
            <div class="bento-card-icon" style="background:rgba(200,169,110,.12)">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold)"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <h3 class="bento-card-title">Congés & Absences</h3>
            <p class="bento-card-desc">Demandes, approbations et soldes de congés. Calendrier d'équipe avec visibilité complète.</p>
            <div class="feat-highlights">
              <span class="feat-chip"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Workflow</span>
              <span class="feat-chip"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Soldes auto</span>
            </div>
          </div>

          <!-- ── ROW 3 ── -->
          <!-- Paie -->
          <div class="bento-card reveal">
            <div class="bento-card-icon" style="background:rgba(27,60,53,.08)">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--green)"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <h3 class="bento-card-title">Paie automatisée</h3>
            <p class="bento-card-desc">Fiches de paie conformes aux barèmes locaux (CNPS, IRPP). Export PDF instantané.</p>
            <div class="feat-highlights">
              <span class="feat-chip"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>CNPS</span>
              <span class="feat-chip"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>PDF</span>
            </div>
          </div>

          <!-- Assistant IA (wide) -->
          <div class="bento-card bento-card-wide reveal" style="background:var(--green);border-color:var(--green)">
            <div style="display:flex;gap:1.5rem;align-items:flex-start">
              <div class="bento-card-icon" style="background:rgba(245,240,232,.15)">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--cream)"><path d="M12 2a4 4 0 014 4v2H8V6a4 4 0 014-4z" opacity=".6"/><rect x="4" y="8" width="16" height="12" rx="2"/><circle cx="9" cy="14" r="1" fill="var(--cream)"/><circle cx="15" cy="14" r="1" fill="var(--cream)"/></svg>
              </div>
              <div style="flex:1">
                <h3 class="bento-card-title" style="color:var(--cream)">Assistant IA intégré</h3>
                <p class="bento-card-desc" style="color:rgba(245,240,232,.6);max-width:400px">Chatbot RH propulsé par l'IA avec accès complet aux données. Réponses instantanées sur congés, présences, tâches et analyses.</p>
                <div class="feat-highlights" style="margin-top:1rem">
                  <span class="feat-chip" style="background:rgba(245,240,232,.1);border-color:rgba(245,240,232,.15);color:rgba(245,240,232,.7)"><svg viewBox="0 0 24 24" style="stroke:var(--gold)"><polyline points="20 6 9 17 4 12"/></svg>Analyse données</span>
                  <span class="feat-chip" style="background:rgba(245,240,232,.1);border-color:rgba(245,240,232,.15);color:rgba(245,240,232,.7)"><svg viewBox="0 0 24 24" style="stroke:var(--gold)"><polyline points="20 6 9 17 4 12"/></svg>Chat naturel</span>
                  <span class="feat-chip" style="background:rgba(245,240,232,.1);border-color:rgba(245,240,232,.15);color:rgba(245,240,232,.7)"><svg viewBox="0 0 24 24" style="stroke:var(--gold)"><polyline points="20 6 9 17 4 12"/></svg>Rapports auto</span>
                </div>
              </div>
            </div>
          </div>
      </div>
    </section>

    <!-- ═══ INTEGRATIONS ═══ -->
    <section class="integrations-section" id="integrations">
      <div class="container">
        <h2 class="section-title reveal">Ne remplacez rien. <em style="font-style:italic;color:var(--gold)">Intégrez.</em></h2>
        <p class="section-desc reveal" style="margin-top:1rem">ManageX s'intègre nativement avec vos outils existants pour un écosystème RH unifié.</p>
        <div class="integrations-grid reveal">
          <!-- Email -->
          <div class="integration-icon" title="Email & Notifications">
            <svg viewBox="0 0 24 24" width="32" height="32"><path d="M2 6l10 7 10-7v12H2z" fill="none" stroke="#EA4335" stroke-width="1.5"/><path d="M22 6L12 13 2 6" fill="none" stroke="#EA4335" stroke-width="1.5"/><rect x="2" y="6" width="20" height="12" rx="2" fill="none" stroke="#EA4335" stroke-width="1.5"/></svg>
          </div>
          <!-- Excel -->
          <div class="integration-icon" title="Export Excel">
            <svg viewBox="0 0 24 24" width="32" height="32"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z" fill="none" stroke="#217346" stroke-width="1.5"/><path d="M14 2v6h6" fill="none" stroke="#217346" stroke-width="1.5"/><path d="M8 13l3 5m0-5l-3 5M15 13v5m-2-5h4" fill="none" stroke="#217346" stroke-width="1.5" stroke-linecap="round"/></svg>
          </div>
          <!-- PDF -->
          <div class="integration-icon" title="Génération PDF">
            <svg viewBox="0 0 24 24" width="32" height="32"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z" fill="none" stroke="#E53935" stroke-width="1.5"/><path d="M14 2v6h6" fill="none" stroke="#E53935" stroke-width="1.5"/><text x="12" y="17" text-anchor="middle" font-size="5" font-weight="700" fill="#E53935">PDF</text></svg>
          </div>
          <!-- Géolocalisation -->
          <div class="integration-icon" title="Géolocalisation GPS">
            <svg viewBox="0 0 24 24" width="32" height="32"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1118 0z" fill="none" stroke="#EA4335" stroke-width="1.5"/><circle cx="12" cy="10" r="3" fill="none" stroke="#EA4335" stroke-width="1.5"/></svg>
          </div>
          <!-- Push Notifications -->
          <div class="integration-icon" title="Push Notifications">
            <svg viewBox="0 0 24 24" width="32" height="32"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" fill="none" stroke="#FFCA28" stroke-width="1.5"/><path d="M13.73 21a2 2 0 01-3.46 0" fill="none" stroke="#FFCA28" stroke-width="1.5"/><line x1="12" y1="2" x2="12" y2="4" stroke="#FFCA28" stroke-width="1.5"/></svg>
          </div>
          <!-- SSL / Security -->
          <div class="integration-icon" title="Sécurité SSL">
            <svg viewBox="0 0 24 24" width="32" height="32"><rect x="3" y="11" width="18" height="11" rx="2" fill="none" stroke="#43A047" stroke-width="1.5"/><path d="M7 11V7a5 5 0 0110 0v4" fill="none" stroke="#43A047" stroke-width="1.5"/><circle cx="12" cy="16" r="1" fill="#43A047"/></svg>
          </div>
          <!-- API -->
          <div class="integration-icon" title="API REST">
            <svg viewBox="0 0 24 24" width="32" height="32"><polyline points="16 18 22 12 16 6" fill="none" stroke="#7C4DFF" stroke-width="1.5" stroke-linecap="round"/><polyline points="8 6 2 12 8 18" fill="none" stroke="#7C4DFF" stroke-width="1.5" stroke-linecap="round"/><line x1="14" y1="4" x2="10" y2="20" stroke="#7C4DFF" stroke-width="1.5" stroke-linecap="round"/></svg>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ TESTIMONIAL ═══ -->
    <section class="testimonial-section section" id="testimonials">
      <div class="container">
        <div class="reveal">
          <div class="testimonial-quote-mark">❝</div>
          <p class="testimonial-text">
            « ManageX aide notre entreprise à réduire les dépenses opérationnelles et le temps de traitement, tout en améliorant la conformité, l'allocation des ressources et l'efficacité de notre gestion des ressources humaines. »
          </p>
          <div class="testimonial-stars">★★★★★</div>
          <div class="testimonial-author-card">
            <div class="testimonial-author-avatar" style="background:var(--green)">AK</div>
            <div class="testimonial-author-info">
              <div class="testimonial-author-name">Aminata Koné</div>
              <div class="testimonial-author-role">DRH — Groupe Solaris</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ STATS ═══ -->
    <section class="stats-section">
      <div class="container">
        <div class="stats-grid">
          <div class="reveal">
            <div class="stat-number" data-count="2024">2024</div>
            <p class="stat-label">Année de lancement</p>
          </div>
          <div class="reveal">
            <div class="stat-number" data-count="150">150+</div>
            <p class="stat-label">Entreprises partenaires</p>
          </div>
          <div class="reveal">
            <div class="stat-number" data-count="5000">5K+</div>
            <p class="stat-label">Employés gérés</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ CTA ═══ -->
    <section class="section" id="contact">
      <div class="container">
        <div class="cta-section reveal">
          <div class="cta-orb" style="width:300px;height:300px;top:-100px;right:-80px;background:rgba(200,169,110,.2)" aria-hidden="true"></div>
          <div class="cta-orb" style="width:250px;height:250px;bottom:-80px;left:-60px;background:rgba(61,122,106,.2)" aria-hidden="true"></div>
          <div style="position:relative">
            <h2 class="section-title" style="margin-bottom:.75rem">Découvrez tout le potentiel<br>de <span style="color:var(--gold)">ManageX</span></h2>
            <p class="section-desc" style="margin-bottom:2rem">Rejoignez les entreprises qui ont choisi ManageX pour moderniser leur gestion RH.</p>
            <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap">
              <a href="{{ route('demo-request') }}" class="btn btn-gold" style="padding:.85rem 2rem">Demander une démo</a>
              <a href="{{ route('login') }}" class="btn" style="padding:.85rem 2rem;background:rgba(245,240,232,.15);color:var(--cream);border:1px solid rgba(245,240,232,.2)">Se connecter →</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- ═══ FOOTER ═══ -->
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <div class="footer-logo">
            <img src="{{ asset('images/managex_logo.png') }}" alt="ManageX Logo">
          </div>
          <p class="footer-desc">La plateforme RH nouvelle génération propulsée par l'IA. Conçue pour les entreprises ambitieuses d'Afrique et du monde.</p>
          <div style="margin-top:1.25rem;display:flex;flex-direction:column;gap:.35rem">
            <a href="mailto:contact@ya-consulting.com" style="font-size:.8rem;color:rgba(245,240,232,.5)">📧 contact@ya-consulting.com</a>
            <span style="font-size:.8rem;color:rgba(245,240,232,.35)">📍 Abidjan, Côte d'Ivoire</span>
          </div>
        </div>
        <div class="footer-col">
          <h3 class="footer-col-title">Produit</h3>
          <ul>
            <li><a href="#features">Fonctionnalités</a></li>
            <li><a href="#integrations">Intégrations</a></li>
            <li><a href="{{ route('demo-request') }}">Démo</a></li>
            <li><a href="{{ route('login') }}">Connexion</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h3 class="footer-col-title">Entreprise</h3>
          <ul>
            <li><a href="#testimonials">Témoignages</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="{{ url('/') }}">Accueil</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h3 class="footer-col-title">Ressources</h3>
          <ul>
            <li><a href="#">Documentation</a></li>
            <li><a href="#">Support</a></li>
            <li><a href="#">Politique de confidentialité</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} ManageX — YA Consulting. Tous droits réservés.</span>
        <div class="footer-socials">
          <a href="#" class="footer-social" aria-label="LinkedIn"><svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6zM2 9h4v12H2zM4 2a2 2 0 110 4 2 2 0 010-4z"/></svg></a>
          <a href="#" class="footer-social" aria-label="Twitter/X"><svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
          <a href="#" class="footer-social" aria-label="Facebook"><svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
          <a href="#" class="footer-social" aria-label="Instagram"><svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>
        </div>
      </div>
    </div>
  </footer>

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

    // Nav scroll effect
    window.addEventListener('scroll',()=>{
      document.getElementById('nav').classList.toggle('scrolled',window.scrollY>20);
    });

    // Mini chart bars
    const chartEl=document.getElementById('heroChart');
    if(chartEl){
      const heights=[40,58,50,68,62,78,72,82,88,68,55,75,92,70,80,85,60,65,78,82];
      heights.forEach((h,i)=>{
        const b=document.createElement('div');
        b.className='mini-bar';
        b.style.height='4px';
        b.style.transitionDelay=i*30+'ms';
        chartEl.appendChild(b);
      });
      const obs=new IntersectionObserver(entries=>{
        entries.forEach(e=>{
          if(e.isIntersecting){
            e.target.querySelectorAll('.mini-bar').forEach((b,i)=>{
              setTimeout(()=>{b.style.height=heights[i]+'%'},200);
            });
            obs.unobserve(e.target);
          }
        });
      },{threshold:.3});
      obs.observe(chartEl);
    }

    // Scroll reveal
    const allRevealEls=document.querySelectorAll('.reveal');
    const revealObs=new IntersectionObserver((entries)=>{
      entries.forEach(e=>{
        if(e.isIntersecting){
          e.target.classList.add('visible');
          revealObs.unobserve(e.target);
        }
      });
    },{threshold:0.01,rootMargin:'0px 0px 50px 0px'});

    // Stagger children in grids
    document.querySelectorAll('.bento-grid,.bento-row,.stats-grid,.integrations-grid').forEach(grid=>{
      grid.querySelectorAll('.reveal,.bento-card,.integration-icon').forEach((child,i)=>{
        if(child.classList.contains('reveal'))child.style.transitionDelay=(i*0.1)+'s';
      });
    });

    allRevealEls.forEach(el=>revealObs.observe(el));

    // Fallback: reveal elements already in viewport
    requestAnimationFrame(()=>{
      allRevealEls.forEach(el=>{
        const rect=el.getBoundingClientRect();
        if(rect.top<window.innerHeight&&rect.bottom>0){
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
