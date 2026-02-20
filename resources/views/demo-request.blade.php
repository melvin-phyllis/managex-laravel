<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Demander une démo — ManageX</title>
  <meta name="description" content="Demandez une démonstration gratuite de ManageX, la plateforme RH intelligente." />
  <meta name="theme-color" content="#1B3C35">
  <link rel="canonical" href="{{ route('demo-request') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <style>
    /* === RESET === */
    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --green: #1B3C35;
      --green-light: #2D5A4E;
      --green-accent: #3D7A6A;
      --cream: #F5F0E8;
      --cream-light: #FAF7F2;
      --cream-dark: #ECE5D8;
      --gold: #C8A96E;
      --gold-light: #D4BC8B;
      --fg: #1B3C35;
      --muted: #5C6E68;
    }

    html {
      scroll-behavior: smooth;
      font-size: 16px;
      -webkit-font-smoothing: antialiased;
    }

    body {
      font-family: 'DM Sans', system-ui, sans-serif;
      background: #FAF7F2;
      color: #1B3C35;
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    a { text-decoration: none; color: inherit; }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1.5rem;
    }

    /* === NAV === */
    .nav {
      background: #FAF7F2;
      border-bottom: 1px solid #ECE5D8;
    }

    .nav-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 110px;
    }

    .nav-logo {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 1rem;
      font-weight: 700;
      color: #1B3C35;
    }

    .nav-logo img {
      height: 80px;
      width: auto;
      object-fit: contain;
    }

    .nav-right {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .nav-link {
      font-size: 0.85rem;
      color: #5C6E68;
      font-weight: 500;
      transition: color 0.2s;
    }

    .nav-link:hover { color: #1B3C35; }

    .btn-nav {
      padding: 0.5rem 1.15rem;
      border-radius: 999px;
      font-size: 0.8rem;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: all 0.25s;
      font-family: inherit;
      display: inline-flex;
      align-items: center;
    }

    .btn-nav-outline {
      background: transparent;
      border: 1.5px solid #ECE5D8;
      color: #1B3C35;
    }

    .btn-nav-outline:hover {
      border-color: #1B3C35;
      background: rgba(27,60,53,0.03);
    }

    .btn-nav-primary {
      background: #1B3C35;
      color: #F5F0E8;
    }

    .btn-nav-primary:hover { background: #2D5A4E; }

    /* === HERO HEADER === */
    .demo-hero {
      text-align: center;
      padding: 3.5rem 1.5rem 2rem;
    }

    .demo-hero-tag {
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      color: #3D7A6A;
      margin-bottom: 1rem;
    }

    .demo-hero-title {
      font-family: 'DM Serif Display', Georgia, serif;
      font-size: 2.6rem;
      color: #1B3C35;
      line-height: 1.15;
      margin-bottom: 0.75rem;
    }

    .demo-hero-desc {
      font-size: 0.95rem;
      color: #5C6E68;
      max-width: 520px;
      margin: 0 auto;
      line-height: 1.7;
    }

    /* === SPLIT LAYOUT === */
    .demo-content {
      flex: 1;
      padding: 0 1.5rem 4rem;
    }

    .demo-grid {
      display: grid;
      grid-template-columns: 1fr 1.15fr;
      gap: 1.5rem;
      max-width: 1000px;
      margin: 0 auto;
    }

    /* === Left: Testimonial === */
    .demo-testimonial {
      background: #F5F0E8;
      border: 1px solid #ECE5D8;
      border-radius: 1.25rem;
      padding: 2.5rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .testimonial-quote-mark {
      font-family: 'DM Serif Display', serif;
      font-size: 3.5rem;
      line-height: 1;
      color: #1B3C35;
      opacity: 0.2;
      margin-bottom: 0.5rem;
    }

    .testimonial-quote {
      font-size: 1.05rem;
      line-height: 1.7;
      color: #1B3C35;
      font-style: italic;
      margin-bottom: 2rem;
    }

    .testimonial-author {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 2.5rem;
    }

    .testimonial-avatar {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: #1B3C35;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 0.7rem;
      font-weight: 700;
      flex-shrink: 0;
    }

    .testimonial-name {
      font-size: 0.85rem;
      font-weight: 600;
      color: #1B3C35;
    }

    .testimonial-role {
      font-size: 0.75rem;
      color: #5C6E68;
    }

    .testimonial-footer {
      font-size: 0.8rem;
      color: #5C6E68;
      line-height: 1.6;
      border-top: 1px solid #ECE5D8;
      padding-top: 1.5rem;
    }

    .testimonial-footer strong {
      color: #1B3C35;
      font-weight: 600;
    }

    /* === Right: Form card === */
    .demo-form-card {
      background: #ffffff;
      border: 1px solid #ECE5D8;
      border-radius: 1.25rem;
      padding: 2.5rem;
      box-shadow: 0 8px 30px rgba(27,60,53,0.1);
    }

    /* === FORM STYLES === */
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .form-group {
      margin-bottom: 1.15rem;
    }

    .form-label {
      display: block;
      font-size: 0.8rem;
      font-weight: 600;
      color: #1B3C35;
      margin-bottom: 0.4rem;
    }

    .form-label .req {
      color: #3D7A6A;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    select,
    textarea {
      display: block;
      width: 100%;
      padding: 0.8rem 1rem;
      border: 1.5px solid #ECE5D8;
      border-radius: 0.75rem;
      font-family: 'DM Sans', system-ui, sans-serif;
      font-size: 0.875rem;
      color: #1B3C35;
      background-color: #FAF7F2;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="tel"]:focus,
    select:focus,
    textarea:focus {
      border-color: #3D7A6A;
      background-color: #ffffff;
      box-shadow: 0 0 0 3px rgba(61,122,106,0.1);
    }

    input::placeholder,
    textarea::placeholder {
      color: rgba(92,110,104,0.4);
    }

    select {
      background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%235C6E68' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 0.85rem center;
      background-size: 16px;
      padding-right: 2.5rem;
      cursor: pointer;
    }

    textarea {
      resize: vertical;
      min-height: 90px;
    }

    /* Submit button */
    .btn-submit {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      width: 100%;
      padding: 0.9rem;
      border: none;
      border-radius: 999px;
      background: #1B3C35;
      color: #F5F0E8;
      font-family: 'DM Sans', system-ui, sans-serif;
      font-size: 0.9rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.25s;
      box-shadow: 0 4px 14px rgba(27,60,53,0.2);
      margin-top: 0.5rem;
    }

    .btn-submit:hover {
      background: #2D5A4E;
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(27,60,53,0.25);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    .btn-submit svg {
      width: 16px;
      height: 16px;
      stroke: currentColor;
      fill: none;
      stroke-width: 2.5;
    }

    /* Alerts */
    .alert {
      padding: 0.85rem 1.15rem;
      border-radius: 0.75rem;
      font-size: 0.85rem;
      margin-bottom: 1.5rem;
      line-height: 1.5;
    }

    .alert-success {
      background: rgba(45,90,78,0.08);
      border: 1px solid rgba(45,90,78,0.2);
      color: #2D5A4E;
    }

    .alert-error {
      background: rgba(220,38,38,0.06);
      border: 1px solid rgba(220,38,38,0.15);
      color: #b91c1c;
    }

    /* === FOOTER === */
    .footer {
      border-top: 1px solid #ECE5D8;
      background: #FAF7F2;
      padding: 1.5rem 0;
      text-align: center;
      font-size: 0.75rem;
      color: #5C6E68;
    }

    .footer a {
      color: #3D7A6A;
      font-weight: 500;
    }

    /* === ANIMATIONS === */
    @keyframes fadeUp {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    .anim-1 { animation: fadeUp 0.6s ease-out both; }
    .anim-2 { animation: fadeUp 0.6s ease-out 0.1s both; }
    .anim-3 { animation: fadeUp 0.6s ease-out 0.2s both; }

    /* === RESPONSIVE === */
    @media (max-width: 800px) {
      .demo-grid {
        grid-template-columns: 1fr;
      }
      .demo-testimonial { order: 2; }
      .demo-form-card { order: 1; }
      .form-row { grid-template-columns: 1fr; }
      .nav-right .nav-link { display: none; }
      .demo-hero-title { font-size: 2rem; }
    }

    @media (max-width: 480px) {
      .demo-form-card { padding: 1.5rem; }
      .demo-testimonial { padding: 1.5rem; }
      .demo-hero { padding: 2rem 1rem 1.5rem; }
      .demo-hero-title { font-size: 1.7rem; }
    }
  </style>
</head>
<body>
  <!-- NAV -->
  <header class="nav">
    <div class="container nav-inner">
      <a href="{{ url('/') }}" class="nav-logo">
        <img src="{{ asset('images/managex_logo.png') }}" alt="ManageX Logo">
      </a>
      <div class="nav-right">
        <a href="{{ url('/') }}" class="nav-link">Accueil</a>
        <a href="{{ url('/') }}#features" class="nav-link">Fonctionnalités</a>
        <a href="{{ route('login') }}" class="btn-nav btn-nav-outline">Connexion</a>
        <a href="#demo-form" class="btn-nav btn-nav-primary">Démo</a>
      </div>
    </div>
  </header>

  <!-- HERO -->
  <section class="demo-hero">
    <p class="demo-hero-tag anim-1">Demander une démo</p>
    <h1 class="demo-hero-title anim-2">Planifiez une démonstration<br>gratuite de ManageX</h1>
    <p class="demo-hero-desc anim-3">Comment ManageX peut transformer votre gestion RH ? Planifiez une démo gratuite et obtenez toutes vos réponses.</p>
  </section>

  <!-- SPLIT CONTENT -->
  <section class="demo-content">
    <div class="demo-grid">
      <!-- LEFT: Testimonial -->
      <div class="demo-testimonial anim-2">
        <div>
          <div class="testimonial-quote-mark">"</div>
          <p class="testimonial-quote">
            Ce que nous apprécions le plus chez ManageX, c'est la simplicité d'utilisation et les fonctionnalités parfaitement adaptées à la gestion RH. L'équipe est toujours disponible pour nous accompagner.
          </p>
          <div class="testimonial-author">
            <div class="testimonial-avatar">AK</div>
            <div>
              <div class="testimonial-name">Aminata Koné</div>
              <div class="testimonial-role">DRH — Groupe Solaris</div>
            </div>
          </div>
        </div>
        <div class="testimonial-footer">
          <strong>ManageX</strong> n'est pas qu'un simple logiciel RH — c'est un écosystème complet. Explorez nos fonctionnalités avancées : pointage géolocalisé, paie automatisée, assistant IA, et bien plus encore, le tout adapté à vos besoins.
        </div>
      </div>

      <!-- RIGHT: Form -->
      <div class="demo-form-card anim-3" id="demo-form">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
          <div class="alert alert-error">
            @foreach($errors->all() as $error)
              <p>{{ $error }}</p>
            @endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('demo-request.store') }}">
          @csrf

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="contact_name">Nom complet <span class="req">*</span></label>
              <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" placeholder="Votre nom" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="company_name">Entreprise <span class="req">*</span></label>
              <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Nom de l'entreprise" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="email">Email professionnel <span class="req">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="votre@entreprise.com" required>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="company_size">Taille de l'entreprise <span class="req">*</span></label>
              <select id="company_size" name="company_size" required>
                <option value="" disabled {{ old('company_size') ? '' : 'selected' }}>Sélectionnez</option>
                <option value="1-10" {{ old('company_size') === '1-10' ? 'selected' : '' }}>1 — 10 employés</option>
                <option value="11-50" {{ old('company_size') === '11-50' ? 'selected' : '' }}>11 — 50 employés</option>
                <option value="51-200" {{ old('company_size') === '51-200' ? 'selected' : '' }}>51 — 200 employés</option>
                <option value="200+" {{ old('company_size') === '200+' ? 'selected' : '' }}>200+ employés</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="phone">Téléphone</label>
              <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+225 00 00 00 00">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="message">Vos besoins (optionnel)</label>
            <textarea id="message" name="message" placeholder="Décrivez vos défis RH actuels ou posez vos questions...">{{ old('message') }}</textarea>
          </div>

          <button type="submit" class="btn-submit">
            Obtenir ma démo gratuite
            <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </button>
        </form>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <span>&copy; {{ date('Y') }} ManageX — <a href="https://ya-consulting.com" target="_blank">YA Consulting</a>. Tous droits réservés.</span>
    </div>
  </footer>
</body>
</html>
