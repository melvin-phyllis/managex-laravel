<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Demander une démo — ManageX</title>
  <meta name="description" content="Demandez une démonstration gratuite de ManageX, la plateforme RH intelligente." />
  <meta name="theme-color" content="#5680E9">
  <link rel="canonical" href="{{ route('demo-request') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
    :root{
      --primary:#5680E9;--secondary:#5AB9EA;--accent:#8860D0;
      --border:#C1C8E4;--bg:#f8fafc;--card:#fff;--fg:#1e293b;--muted:#64748b;
      --shadow-soft:0 10px 30px -18px rgba(86,128,233,.35);
      --shadow-md:0 20px 50px -20px rgba(86,128,233,.22);
      --gradient-logo:linear-gradient(90deg,#3158a8,#5AB9EA);
      --radius:0.9rem;--radius-lg:1.25rem;
    }
    html{scroll-behavior:smooth;font-size:16px;-webkit-font-smoothing:antialiased}
    body{font-family:Figtree,system-ui,sans-serif;background:var(--bg);color:var(--fg);line-height:1.6;min-height:100vh;display:flex;flex-direction:column}
    a{text-decoration:none;color:inherit}
    .container{max-width:1200px;margin:0 auto;padding:0 1.5rem}
    .text-gradient{background:var(--gradient-logo);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

    /* Nav */
    .nav{position:sticky;top:0;z-index:50;background:rgba(255,255,255,.82);backdrop-filter:blur(16px);border-bottom:1px solid rgba(193,200,228,.5)}
    .nav-inner{display:flex;align-items:center;justify-content:space-between;height:64px}
    .nav-logo{display:inline-flex;align-items:center;gap:.5rem;font-size:1rem;font-weight:700}
    .nav-logo-icon{width:36px;height:36px;border-radius:var(--radius);background:rgba(86,128,233,.1);border:1px solid rgba(193,200,228,.5);display:flex;align-items:center;justify-content:center}
    .nav-back{display:inline-flex;align-items:center;gap:.5rem;font-size:.875rem;color:var(--muted);transition:color .2s}
    .nav-back:hover{color:var(--primary)}

    /* Form section */
    .demo-section{flex:1;display:flex;align-items:center;justify-content:center;padding:3rem 1.5rem}
    .demo-card{width:100%;max-width:560px;background:var(--card);border:1px solid rgba(193,200,228,.5);border-radius:var(--radius-lg);box-shadow:var(--shadow-md);padding:2.5rem}
    .demo-title{font-size:1.5rem;font-weight:700;margin-bottom:.25rem}
    .demo-subtitle{font-size:.875rem;color:var(--muted);margin-bottom:2rem}

    .form-group{margin-bottom:1.25rem}
    .form-label{display:block;font-size:.8rem;font-weight:600;color:var(--muted);margin-bottom:.4rem}
    .form-input,.form-select,.form-textarea{width:100%;padding:.7rem .9rem;border:1px solid rgba(193,200,228,.6);border-radius:var(--radius);font:inherit;font-size:.875rem;outline:none;transition:border-color .2s,box-shadow .2s;background:#fff}
    .form-input:focus,.form-select:focus,.form-textarea:focus{border-color:rgba(86,128,233,.5);box-shadow:0 0 0 3px rgba(86,128,233,.1)}
    .form-textarea{resize:vertical;min-height:80px}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    .form-error{font-size:.75rem;color:#dc2626;margin-top:.25rem}

    .btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.8rem 1.5rem;border-radius:var(--radius);font-size:.875rem;font-weight:600;border:none;cursor:pointer;transition:all .2s;width:100%}
    .btn:hover{transform:translateY(-2px)}
    .btn-primary{background:var(--primary);color:#fff;box-shadow:var(--shadow-soft)}
    .btn-primary:hover{box-shadow:var(--shadow-md)}

    .alert{padding:.75rem 1rem;border-radius:var(--radius);font-size:.875rem;margin-bottom:1.5rem}
    .alert-success{background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.2);color:#059669}
    .alert-error{background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);color:#dc2626}

    /* Footer */
    .footer{border-top:1px solid rgba(193,200,228,.5);background:#fff;padding:1.5rem 0;text-align:center;font-size:.75rem;color:var(--muted)}

    @media(max-width:767px){
      .demo-card{padding:1.5rem}
      .form-row{grid-template-columns:1fr}
      .container{padding:0 1rem}
    }
  </style>
</head>
<body>
  <!-- NAV -->
  <header class="nav">
    <div class="container nav-inner">
      <a href="{{ url('/') }}" class="nav-logo">
        <span class="nav-logo-icon">
          <img src="{{ asset('images/managex_logo.png') }}" alt="ManageX Logo" style="width:28px;height:28px;border-radius:50%;object-fit:cover">
        </span>
        <span class="text-gradient">ManageX</span>
      </a>
      <a href="{{ url('/') }}" class="nav-back">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Retour à l'accueil
      </a>
    </div>
  </header>

  <!-- FORM -->
  <section class="demo-section">
    <div class="demo-card">
      <h1 class="demo-title">Demander une <span class="text-gradient">démo</span></h1>
      <p class="demo-subtitle">Remplissez le formulaire ci-dessous et notre équipe vous contactera sous 24h pour planifier votre démonstration personnalisée.</p>

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
            <label class="form-label" for="company_name">Nom de l'entreprise *</label>
            <input type="text" id="company_name" name="company_name" class="form-input" value="{{ old('company_name') }}" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="contact_name">Nom du contact *</label>
            <input type="text" id="contact_name" name="contact_name" class="form-input" value="{{ old('contact_name') }}" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="email">Email professionnel *</label>
            <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="phone">Téléphone</label>
            <input type="tel" id="phone" name="phone" class="form-input" value="{{ old('phone') }}">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="company_size">Taille de l'entreprise *</label>
          <select id="company_size" name="company_size" class="form-select" required>
            <option value="" disabled {{ old('company_size') ? '' : 'selected' }}>Sélectionnez</option>
            <option value="1-10" {{ old('company_size') === '1-10' ? 'selected' : '' }}>1 - 10 employés</option>
            <option value="11-50" {{ old('company_size') === '11-50' ? 'selected' : '' }}>11 - 50 employés</option>
            <option value="51-200" {{ old('company_size') === '51-200' ? 'selected' : '' }}>51 - 200 employés</option>
            <option value="200+" {{ old('company_size') === '200+' ? 'selected' : '' }}>200+ employés</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="message">Message (optionnel)</label>
          <textarea id="message" name="message" class="form-textarea" placeholder="Décrivez vos besoins ou posez vos questions...">{{ old('message') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
          Envoyer ma demande
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
      </form>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <span>&copy; {{ date('Y') }} ManageX. Tous droits réservés.</span>
    </div>
  </footer>
</body>
</html>
