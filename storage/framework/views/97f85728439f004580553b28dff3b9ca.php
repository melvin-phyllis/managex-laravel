<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ManageX — La plateforme RH nouvelle génération. Gestion des présences, paie, congés, tâches et analytics, propulsée par l'IA.">
    <title>ManageX — Gestion RH Intelligente</title>
    <script>
        // Prevent flash of wrong theme on load
        (function() {
            var saved = localStorage.getItem('managex-theme');
            var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <style>
        /* ========================================
           CSS VARIABLES & RESET
        ======================================== */
        :root {
            --white: #ffffff;
            --off-white: #f8f9fc;
            --light: #f0f1f6;
            --light-surface: #e8e9f0;
            --light-card: #ffffff;
            --black: #0f1023;
            --dark-text: #1a1b2e;
            --gray-100: #2d2e42;
            --gray-300: #5a5b72;
            --gray-500: #8b8ca2;
            --gray-600: #b0b1c4;
            --gold: #b08a2e;
            --gold-light: #d4a830;
            --accent: #6c4cec;
            --accent-light: #7c5cfc;
            --accent-glow: rgba(108, 76, 236, 0.18);
            --indigo: #4f46e5;
            --violet: #7c3aed;
            --emerald: #059669;
            --border: rgba(0, 0, 0, 0.07);
            --border-hover: rgba(108, 76, 236, 0.2);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.04);
            --shadow-md: 0 8px 30px rgba(0,0,0,0.06);
            --shadow-lg: 0 20px 60px rgba(0,0,0,0.08);
            --font-serif: 'Georgia', 'Times New Roman', serif;
            --font-sans: 'Segoe UI', system-ui, -apple-system, sans-serif;
            --transition: cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --transition-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* ========================================
           DARK THEME OVERRIDE
        ======================================== */
        [data-theme="dark"] {
            --white: #0a0a0f;
            --off-white: #111118;
            --light: #1a1a24;
            --light-surface: #22222e;
            --light-card: #151520;
            --black: #f0f1f6;
            --dark-text: #e8e9f0;
            --gray-100: #d0d1e0;
            --gray-300: #a0a1b4;
            --gray-500: #6b6c82;
            --gray-600: #4a4b5e;
            --gold: #d4a830;
            --gold-light: #e8c040;
            --accent: #7c5cfc;
            --accent-light: #8c6cff;
            --accent-glow: rgba(124, 92, 252, 0.25);
            --border: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(124, 92, 252, 0.3);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.2);
            --shadow-md: 0 8px 30px rgba(0,0,0,0.3);
            --shadow-lg: 0 20px 60px rgba(0,0,0,0.4);
        }

        [data-theme="dark"] body::after {
            opacity: 0.03;
        }

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            font-family: var(--font-sans);
            background: var(--white);
            color: var(--dark-text);
            overflow-x: hidden;
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        ::selection {
            background: var(--accent);
            color: #ffffff;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            max-width: 100%;
            display: block;
        }

        /* ========================================
           GRAIN / TEXTURE OVERLAY (subtle)
        ======================================== */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 9999;
            opacity: 0.015;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='1'/%3E%3C/svg%3E");
            background-repeat: repeat;
        }

        /* ========================================
           UTILITY CLASSES
        ======================================== */
        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-pad {
            padding: 8rem 0;
        }

        .text-gold { color: var(--gold); }
        .text-accent { color: var(--accent); }
        .text-gray { color: var(--gray-300); }
        .text-center { text-align: center; }

        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s var(--transition), transform 0.8s var(--transition);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }
        .reveal-delay-5 { transition-delay: 0.5s; }

        @media (prefers-reduced-motion: reduce) {
            .reveal {
                opacity: 1;
                transform: none;
                transition: none;
            }
            .parallax-bg {
                transform: none !important;
            }
        }

        /* ========================================
           NAVIGATION
        ======================================== */
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 1.25rem 0;
            transition: all 0.4s var(--transition);
        }

        .nav.scrolled {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px) saturate(1.8);
            -webkit-backdrop-filter: blur(20px) saturate(1.8);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 0;
            box-shadow: var(--shadow-sm);
        }

        [data-theme="dark"] .nav.scrolled {
            background: rgba(10, 10, 15, 0.85);
        }

        .nav .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-logo {
            font-family: var(--font-sans);
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--dark-text);
        }

        .nav-logo .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--indigo));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 900;
            color: #ffffff;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            list-style: none;
        }

        .nav-links a {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-300);
            transition: color 0.3s;
            letter-spacing: 0.02em;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            border-radius: 1px;
            transition: width 0.3s var(--transition);
        }

        .nav-links a:hover { color: var(--dark-text); }
        .nav-links a:hover::after { width: 100%; }

        .nav-cta {
            padding: 0.6rem 1.5rem;
            background: var(--accent);
            color: #ffffff !important;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s var(--transition);
            border: none;
            cursor: pointer;
        }

        .nav-cta:hover {
            background: var(--accent-light);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px var(--accent-glow);
        }

        .nav-cta::after { display: none !important; }

        .nav-mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--dark-text);
            cursor: pointer;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
        }

        .hamburger {
            width: 24px;
            height: 18px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hamburger span {
            width: 100%;
            height: 2px;
            background: var(--dark-text);
            border-radius: 2px;
            transition: all 0.3s;
        }

        .nav-mobile-toggle.active .hamburger span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        .nav-mobile-toggle.active .hamburger span:nth-child(2) {
            opacity: 0;
        }
        .nav-mobile-toggle.active .hamburger span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* Theme Toggle Button */
        .theme-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(108, 76, 236, 0.08);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.3s var(--transition);
            color: var(--gray-300);
            flex-shrink: 0;
        }

        .theme-toggle:hover {
            background: rgba(108, 76, 236, 0.15);
            border-color: var(--border-hover);
            color: var(--accent);
        }

        .theme-toggle svg {
            width: 18px;
            height: 18px;
            transition: transform 0.3s var(--transition);
        }

        .theme-toggle:hover svg {
            transform: rotate(15deg);
        }

        /* Light mode: show moon (click to go dark), hide sun */
        .theme-toggle .icon-sun { display: none; }
        .theme-toggle .icon-moon { display: block; }

        /* Dark mode: show sun (click to go light), hide moon */
        [data-theme="dark"] .theme-toggle .icon-sun { display: block; }
        [data-theme="dark"] .theme-toggle .icon-moon { display: none; }

        /* ========================================
           HERO SECTION
        ======================================== */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .parallax-bg {
            position: absolute;
            inset: -15%;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(108, 76, 236, 0.07) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(79, 70, 229, 0.05) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(124, 58, 237, 0.04) 0%, transparent 50%),
                linear-gradient(180deg, var(--white) 0%, var(--off-white) 100%);
            will-change: transform;
        }

        [data-theme="dark"] .parallax-bg {
            background:
                radial-gradient(ellipse at 20% 50%, rgba(124, 92, 252, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(79, 70, 229, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(124, 58, 237, 0.06) 0%, transparent 50%),
                linear-gradient(180deg, var(--white) 0%, var(--off-white) 100%);
        }

        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(108, 76, 236, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(108, 76, 236, 0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);
        }

        [data-theme="dark"] .hero-grid {
            background-image:
                linear-gradient(rgba(124, 92, 252, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124, 92, 252, 0.06) 1px, transparent 1px);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 900px;
            padding: 0 2rem;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            background: rgba(108, 76, 236, 0.08);
            border: 1px solid rgba(108, 76, 236, 0.15);
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--accent);
            margin-bottom: 2rem;
            letter-spacing: 0.03em;
        }

        [data-theme="dark"] .hero-badge {
            background: rgba(124, 92, 252, 0.12);
            border-color: rgba(124, 92, 252, 0.25);
        }

        .hero-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--emerald);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .hero-title {
            font-family: var(--font-serif);
            font-size: clamp(2.5rem, 6vw, 5rem);
            font-weight: 400;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
            color: var(--dark-text);
        }

        .hero-title .accent-word {
            font-style: italic;
            background: linear-gradient(135deg, var(--accent), var(--indigo));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 1.5vw, 1.25rem);
            color: var(--gray-300);
            max-width: 600px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
        }

        .hero-ctas {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--accent), var(--indigo));
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.4s var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--accent-light), var(--violet));
            opacity: 0;
            transition: opacity 0.4s;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px var(--accent-glow);
        }

        .btn-primary:hover::before { opacity: 1; }
        .btn-primary span { position: relative; z-index: 1; }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            color: var(--dark-text);
            font-weight: 500;
            font-size: 0.95rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s var(--transition);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(0,0,0,0.06);
            border-color: rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        [data-theme="dark"] .btn-secondary {
            background: rgba(255,255,255,0.05);
        }

        [data-theme="dark"] .btn-secondary:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.15);
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 4rem;
            padding-top: 3rem;
            border-top: 1px solid var(--border);
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat-value {
            font-family: var(--font-serif);
            font-size: 2rem;
            font-weight: 400;
            color: var(--dark-text);
        }

        .hero-stat-label {
            font-size: 0.8rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            animation: float-orb 8s ease-in-out infinite;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: rgba(108, 76, 236, 0.06);
            top: 10%;
            left: -5%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 300px;
            height: 300px;
            background: rgba(79, 70, 229, 0.05);
            bottom: 10%;
            right: -5%;
            animation-delay: -3s;
        }

        [data-theme="dark"] .orb-1 {
            background: rgba(124, 92, 252, 0.1);
        }

        [data-theme="dark"] .orb-2 {
            background: rgba(79, 70, 229, 0.08);
        }

        @keyframes float-orb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* ========================================
           SECTION HEADERS
        ======================================== */
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-tag {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--accent);
            margin-bottom: 1rem;
            display: block;
        }

        .section-title {
            font-family: var(--font-serif);
            font-size: clamp(2rem, 4vw, 3.2rem);
            font-weight: 400;
            line-height: 1.2;
            margin-bottom: 1rem;
            letter-spacing: -0.01em;
            color: var(--dark-text);
        }

        .section-desc {
            font-size: 1.05rem;
            color: var(--gray-300);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* ========================================
           ABOUT SECTION
        ======================================== */
        .about {
            background: var(--off-white);
            position: relative;
        }

        .about::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(108, 76, 236, 0.2), transparent);
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .about-text h2 {
            font-family: var(--font-serif);
            font-size: clamp(2rem, 3.5vw, 2.8rem);
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: var(--dark-text);
        }

        .about-text p {
            color: var(--gray-300);
            font-size: 1.05rem;
            line-height: 1.8;
            margin-bottom: 1.25rem;
        }

        .about-metrics {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .metric-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.4s var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .metric-card:hover {
            background: var(--white);
            border-color: var(--border-hover);
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .metric-value {
            font-family: var(--font-serif);
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 0.25rem;
        }

        .metric-label {
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        .about-visual {
            position: relative;
        }

        .dashboard-preview {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            position: relative;
        }

        .preview-titlebar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.25rem;
            background: var(--off-white);
            border-bottom: 1px solid var(--border);
        }

        .preview-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .preview-dot-red { background: #ff5f57; }
        .preview-dot-yellow { background: #febc2e; }
        .preview-dot-green { background: #28c840; }

        .preview-body {
            padding: 1.5rem;
        }

        .preview-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .preview-kpi {
            flex: 1;
            background: var(--off-white);
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid var(--border);
        }

        .preview-kpi-label {
            font-size: 0.7rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .preview-kpi-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 0.25rem;
        }

        .preview-kpi-value.green { color: var(--emerald); }
        .preview-kpi-value.accent { color: var(--accent); }
        .preview-kpi-value.gold { color: var(--gold); }

        .preview-chart {
            background: var(--off-white);
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid var(--border);
            margin-top: 0.5rem;
        }

        .preview-chart-label {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-bottom: 0.75rem;
        }

        .chart-bars {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 80px;
        }

        .chart-bar {
            flex: 1;
            border-radius: 4px 4px 0 0;
            background: linear-gradient(180deg, var(--accent), rgba(108, 76, 236, 0.25));
            min-height: 8px;
            transition: height 1s var(--transition);
        }

        .dashboard-glow {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(108, 76, 236, 0.08), transparent);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: -1;
        }

        /* ========================================
           FEATURES SECTION
        ======================================== */
        .features {
            background: var(--white);
            position: relative;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .feature-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.5s var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            opacity: 0;
            transition: opacity 0.5s;
        }

        .feature-card:hover {
            background: var(--white);
            border-color: var(--border-hover);
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
        }

        .feature-card:hover::before { opacity: 1; }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            font-size: 1.4rem;
        }

        .feature-icon.purple {
            background: rgba(108, 76, 236, 0.1);
            color: var(--accent);
        }
        .feature-icon.emerald {
            background: rgba(5, 150, 105, 0.1);
            color: var(--emerald);
        }
        .feature-icon.gold {
            background: rgba(176, 138, 46, 0.1);
            color: var(--gold);
        }
        .feature-icon.indigo {
            background: rgba(79, 70, 229, 0.1);
            color: var(--indigo);
        }
        .feature-icon.rose {
            background: rgba(225, 29, 72, 0.1);
            color: #e11d48;
        }
        .feature-icon.sky {
            background: rgba(2, 132, 199, 0.1);
            color: #0284c7;
        }

        .feature-title {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-text);
        }

        .feature-desc {
            font-size: 0.9rem;
            color: var(--gray-300);
            line-height: 1.6;
        }

        /* ========================================
           AI SECTION
        ======================================== */
        .ai-section {
            background: var(--off-white);
            position: relative;
            overflow: hidden;
        }

        .ai-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(176, 138, 46, 0.25), transparent);
        }

        .ai-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .ai-content h2 {
            font-family: var(--font-serif);
            font-size: clamp(2rem, 3.5vw, 2.8rem);
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: var(--dark-text);
        }

        .ai-content p {
            color: var(--gray-300);
            font-size: 1.05rem;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .ai-features-list {
            list-style: none;
            margin: 1.5rem 0 2rem;
        }

        .ai-features-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.6rem 0;
            font-size: 0.95rem;
            color: var(--gray-300);
        }

        .ai-features-list li .check {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(5, 150, 105, 0.1);
            color: var(--emerald);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.75rem;
            margin-top: 2px;
        }

        .ai-visual {
            position: relative;
        }

        .ai-chat-mock {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .ai-chat-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, var(--violet), var(--indigo));
            color: #ffffff;
        }

        .ai-chat-avatar {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: #ffffff;
        }

        .ai-chat-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #ffffff;
        }

        .ai-chat-status {
            font-size: 0.65rem;
            color: rgba(255,255,255,0.6);
        }

        .ai-chat-body {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .chat-bubble {
            max-width: 80%;
            padding: 0.75rem 1rem;
            border-radius: 14px;
            font-size: 0.85rem;
            line-height: 1.5;
            animation: bubble-in 0.5s var(--transition-bounce);
        }

        .chat-bubble.user {
            align-self: flex-end;
            background: linear-gradient(135deg, var(--accent), var(--indigo));
            color: #ffffff;
            border-bottom-right-radius: 4px;
        }

        .chat-bubble.bot {
            align-self: flex-start;
            background: var(--off-white);
            color: var(--gray-100);
            border: 1px solid var(--border);
            border-bottom-left-radius: 4px;
        }

        @keyframes bubble-in {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .ai-chat-input {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-top: 1px solid var(--border);
        }

        .ai-chat-input input {
            flex: 1;
            background: var(--off-white);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            color: var(--gray-300);
            font-size: 0.8rem;
            outline: none;
        }

        .ai-chat-input button {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), var(--indigo));
            border: none;
            color: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        .ai-badge-float {
            position: absolute;
            top: -10px;
            right: -10px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(176, 138, 46, 0.3);
        }

        /* ========================================
           TESTIMONIALS
        ======================================== */
        .testimonials {
            background: var(--white);
            position: relative;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .testimonial-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.4s var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .testimonial-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .testimonial-stars {
            color: var(--gold);
            font-size: 0.9rem;
            letter-spacing: 2px;
            margin-bottom: 1rem;
        }

        .testimonial-text {
            font-size: 0.95rem;
            color: var(--gray-300);
            line-height: 1.7;
            margin-bottom: 1.5rem;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .testimonial-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--indigo));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: #ffffff;
        }

        .testimonial-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark-text);
        }

        .testimonial-role {
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        /* ========================================
           CTA SECTION
        ======================================== */
        .cta-section {
            background: var(--off-white);
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(108, 76, 236, 0.2), transparent);
        }

        .cta-inner {
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .cta-inner h2 {
            font-family: var(--font-serif);
            font-size: clamp(2rem, 4vw, 3rem);
            margin-bottom: 1rem;
            color: var(--dark-text);
        }

        .cta-inner p {
            font-size: 1.1rem;
            color: var(--gray-300);
            margin-bottom: 2.5rem;
            line-height: 1.7;
        }

        .cta-form {
            display: flex;
            gap: 0.75rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .cta-form input {
            flex: 1;
            padding: 1rem 1.25rem;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--dark-text);
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s;
            box-shadow: var(--shadow-sm);
        }

        .cta-form input::placeholder {
            color: var(--gray-500);
        }

        .cta-form input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .cta-form button {
            white-space: nowrap;
        }

        .cta-success {
            display: none;
            padding: 1rem 2rem;
            background: rgba(5, 150, 105, 0.08);
            border: 1px solid rgba(5, 150, 105, 0.2);
            border-radius: 12px;
            color: var(--emerald);
            font-size: 0.95rem;
            margin-top: 1rem;
            animation: bubble-in 0.5s var(--transition-bounce);
        }

        .cta-success.show { display: block; }

        .cta-orb {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(108, 76, 236, 0.04), transparent);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        /* ========================================
           FOOTER
        ======================================== */
        .footer {
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 4rem 0 2rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-brand p {
            color: var(--gray-500);
            font-size: 0.9rem;
            line-height: 1.7;
            margin-top: 1rem;
            max-width: 300px;
        }

        .footer-col h4 {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--dark-text);
            margin-bottom: 1.25rem;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 0.6rem;
        }

        .footer-col ul a {
            font-size: 0.875rem;
            color: var(--gray-500);
            transition: color 0.3s;
        }

        .footer-col ul a:hover {
            color: var(--accent);
        }

        .footer-bottom {
            padding-top: 2rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-copy {
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        .footer-socials {
            display: flex;
            gap: 1rem;
        }

        .footer-social-link {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--off-white);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-500);
            transition: all 0.3s;
            font-size: 0.85rem;
        }

        .footer-social-link:hover {
            background: rgba(108, 76, 236, 0.08);
            border-color: var(--border-hover);
            color: var(--accent);
        }

        /* ========================================
           RESPONSIVE
        ======================================== */
        @media (max-width: 1024px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .about-grid,
            .ai-grid {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .nav-mobile-toggle { display: flex; }

            .nav-links.open {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                padding: 1.5rem 2rem;
                gap: 1.25rem;
                border-bottom: 1px solid var(--border);
                box-shadow: var(--shadow-md);
            }

            [data-theme="dark"] .nav-links.open {
                background: rgba(10, 10, 15, 0.95);
            }

            .features-grid,
            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .hero-stats {
                gap: 1.5rem;
                flex-wrap: wrap;
            }

            .section-pad {
                padding: 5rem 0;
            }

            .cta-form {
                flex-direction: column;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .hero-stats {
                flex-direction: column;
                gap: 1rem;
            }
            .about-metrics {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- ==================== NAVIGATION ==================== -->
    <nav class="nav" id="nav">
        <div class="container">
            <a href="<?php echo e(url('/')); ?>" class="nav-logo">
                <div class="logo-icon">M</div>
                ManageX
            </a>
            <ul class="nav-links" id="navLinks">
                <li><a href="#about">Plateforme</a></li>
                <li><a href="#features">Fonctionnalités</a></li>
                <li><a href="#ai">Intelligence IA</a></li>
                <li><a href="#testimonials">Témoignages</a></li>
                <li><a href="<?php echo e(route('login')); ?>" class="nav-cta">Se connecter</a></li>
            </ul>
            <button class="theme-toggle" id="themeToggle" aria-label="Changer le thème">
                <svg class="icon-sun" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                <svg class="icon-moon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            </button>
            <button class="nav-mobile-toggle" id="mobileToggle" aria-label="Menu">
                <div class="hamburger">
                    <span></span><span></span><span></span>
                </div>
            </button>
        </div>
    </nav>

    <!-- ==================== HERO ==================== -->
    <section class="hero" id="hero">
        <div class="hero-bg">
            <div class="parallax-bg" id="parallaxBg"></div>
            <div class="hero-grid"></div>
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
        </div>

        <div class="hero-content">
            <div class="hero-badge reveal">
                <span class="dot"></span>
                Plateforme RH nouvelle génération
            </div>

            <h1 class="hero-title reveal reveal-delay-1">
                La gestion RH<br>
                <span class="accent-word">réinventée</span> par l'IA
            </h1>

            <p class="hero-subtitle reveal reveal-delay-2">
                Présences, congés, paie, tâches et analytics — une seule plateforme intelligente pour piloter votre capital humain avec précision.
            </p>

            <div class="hero-ctas reveal reveal-delay-3">
                <a href="<?php echo e(route('login')); ?>" class="btn-primary">
                    <span>Commencer</span>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="position:relative;z-index:1"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="#features" class="btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M10 8l6 4-6 4V8z"/></svg>
                    Voir les fonctionnalités
                </a>
            </div>

            <div class="hero-stats reveal reveal-delay-4">
                <div class="hero-stat">
                    <div class="hero-stat-value">25+</div>
                    <div class="hero-stat-label">Modules intégrés</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">99.9%</div>
                    <div class="hero-stat-label">Disponibilité</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">IA</div>
                    <div class="hero-stat-label">Mistral AI intégré</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">5min</div>
                    <div class="hero-stat-label">Mise en route</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== ABOUT ==================== -->
    <section class="about section-pad" id="about">
        <div class="container">
            <div class="about-grid">
                <div class="about-text reveal">
                    <span class="section-tag">La plateforme</span>
                    <h2>Tout votre écosystème RH, <span class="text-accent">unifié</span></h2>
                    <p>ManageX centralise l'intégralité de vos processus RH dans une interface élégante et intuitive. Du pointage géolocalisé à la génération automatique des fiches de paie, chaque fonctionnalité est pensée pour libérer votre temps et éliminer les frictions.</p>
                    <p>Conçue pour les entreprises exigeantes, la plateforme s'adapte à votre réglementation locale avec un système de paie multi-pays configurable.</p>

                    <div class="about-metrics">
                        <div class="metric-card reveal reveal-delay-1">
                            <div class="metric-value">-70%</div>
                            <div class="metric-label">Temps admin RH réduit</div>
                        </div>
                        <div class="metric-card reveal reveal-delay-2">
                            <div class="metric-value">100%</div>
                            <div class="metric-label">Conformité paie locale</div>
                        </div>
                        <div class="metric-card reveal reveal-delay-3">
                            <div class="metric-value">24/7</div>
                            <div class="metric-label">Assistant IA disponible</div>
                        </div>
                        <div class="metric-card reveal reveal-delay-4">
                            <div class="metric-value">0€</div>
                            <div class="metric-label">Coût d'intégration</div>
                        </div>
                    </div>
                </div>

                <div class="about-visual reveal reveal-delay-2">
                    <div class="dashboard-preview">
                        <div class="preview-titlebar">
                            <div class="preview-dot preview-dot-red"></div>
                            <div class="preview-dot preview-dot-yellow"></div>
                            <div class="preview-dot preview-dot-green"></div>
                            <span style="margin-left:auto;font-size:0.7rem;color:var(--gray-500)">Analytics RH — ManageX</span>
                        </div>
                        <div class="preview-body">
                            <div class="preview-row">
                                <div class="preview-kpi">
                                    <div class="preview-kpi-label">Présents</div>
                                    <div class="preview-kpi-value green">23/25</div>
                                </div>
                                <div class="preview-kpi">
                                    <div class="preview-kpi-label">Tâches</div>
                                    <div class="preview-kpi-value accent">87%</div>
                                </div>
                                <div class="preview-kpi">
                                    <div class="preview-kpi-label">Congés</div>
                                    <div class="preview-kpi-value gold">3</div>
                                </div>
                            </div>
                            <div class="preview-chart">
                                <div class="preview-chart-label">Présences — 30 derniers jours</div>
                                <div class="chart-bars" id="chartBars"></div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-glow"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== FEATURES ==================== -->
    <section class="features section-pad" id="features">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-tag">Fonctionnalités</span>
                <h2 class="section-title">Chaque module, pensé<br>pour l'<span class="text-accent">excellence</span> RH</h2>
                <p class="section-desc">Une suite complète qui couvre l'intégralité du cycle de vie employé, de l'onboarding au départ.</p>
            </div>

            <div class="features-grid">
                <div class="feature-card reveal reveal-delay-1">
                    <div class="feature-icon purple">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                    </div>
                    <h3 class="feature-title">Pointage intelligent</h3>
                    <p class="feature-desc">Check-in/out avec géolocalisation, zones GPS configurables, gestion automatique des retards et récupérations.</p>
                </div>

                <div class="feature-card reveal reveal-delay-2">
                    <div class="feature-icon emerald">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <h3 class="feature-title">Gestion des congés</h3>
                    <p class="feature-desc">Demandes en un clic, soldes automatiques, workflow d'approbation, calendrier d'équipe synchronisé.</p>
                </div>

                <div class="feature-card reveal reveal-delay-3">
                    <div class="feature-icon gold">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <h3 class="feature-title">Paie multi-pays</h3>
                    <p class="feature-desc">Fiches de paie automatisées selon les barèmes locaux. Calculs CNPS, IRPP, primes et retenues configurables.</p>
                </div>

                <div class="feature-card reveal reveal-delay-1">
                    <div class="feature-icon indigo">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>
                    </div>
                    <h3 class="feature-title">Analytics temps réel</h3>
                    <p class="feature-desc">Dashboard interactif, KPIs, graphiques de tendances, taux de présence, top performers et export PDF/Excel.</p>
                </div>

                <div class="feature-card reveal reveal-delay-2">
                    <div class="feature-icon rose">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                    </div>
                    <h3 class="feature-title">Tâches & Kanban</h3>
                    <p class="feature-desc">Assignation, suivi de progression, vues liste/Kanban/calendrier, validation par l'admin et notifications.</p>
                </div>

                <div class="feature-card reveal reveal-delay-3">
                    <div class="feature-icon sky">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    </div>
                    <h3 class="feature-title">Messagerie interne</h3>
                    <p class="feature-desc">Conversations privées et groupes, envoi d'images, messages vocaux, pièces jointes. Temps réel via WebSocket.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== AI SECTION ==================== -->
    <section class="ai-section section-pad" id="ai">
        <div class="container">
            <div class="ai-grid">
                <div class="ai-content reveal">
                    <span class="section-tag">Intelligence artificielle</span>
                    <h2>Un assistant IA qui comprend <span class="text-gold">vos données RH</span></h2>
                    <p>ManageX intègre Mistral AI pour transformer vos données en insights actionnables. L'admin et les employés disposent chacun d'un assistant contextuel dédié.</p>

                    <ul class="ai-features-list">
                        <li>
                            <span class="check">&#10003;</span>
                            <span>Chatbot RH pour les employés (congés, retards, tâches)</span>
                        </li>
                        <li>
                            <span class="check">&#10003;</span>
                            <span>Résumés analytics automatiques pour l'admin</span>
                        </li>
                        <li>
                            <span class="check">&#10003;</span>
                            <span>Contexte temps réel : données entreprise injectées</span>
                        </li>
                        <li>
                            <span class="check">&#10003;</span>
                            <span>Sécurisé : aucune donnée sensible (IBAN, SSN) transmise</span>
                        </li>
                        <li>
                            <span class="check">&#10003;</span>
                            <span>Rate limiting intelligent et cache optimisé</span>
                        </li>
                    </ul>

                    <a href="<?php echo e(route('login')); ?>" class="btn-primary">
                        <span>Tester l'IA</span>
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="position:relative;z-index:1"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="ai-visual reveal reveal-delay-2">
                    <div class="ai-chat-mock" style="position:relative">
                        <div class="ai-badge-float">Mistral AI</div>
                        <div class="ai-chat-header">
                            <div class="ai-chat-avatar">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9.75 3.1v5.71a2.25 2.25 0 01-.66 1.6L5 14.5m4.75-11.4a24.3 24.3 0 014.5 0m0 0v5.71a2.25 2.25 0 00.66 1.6L19 14.5"/></svg>
                            </div>
                            <div>
                                <div class="ai-chat-name">Assistant IA Admin</div>
                                <div class="ai-chat-status">En ligne — Propulsé par Mistral</div>
                            </div>
                        </div>
                        <div class="ai-chat-body" id="aiChatDemo">
                            <div class="chat-bubble user">Résume la situation RH de l'entreprise</div>
                            <div class="chat-bubble bot">Votre effectif compte <strong>25 collaborateurs actifs</strong> (18 CDI, 7 stagiaires) répartis sur 5 départements. Le taux de présence ce mois est de <strong>92%</strong>. Il y a <strong>5 demandes de congé</strong> en attente de validation et <strong>4 tâches</strong> non assignées. Je recommande de traiter les congés en priorité pour éviter les blocages.</div>
                        </div>
                        <div class="ai-chat-input">
                            <input type="text" placeholder="Posez votre question..." disabled>
                            <button disabled>
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== TESTIMONIALS ==================== -->
    <section class="testimonials section-pad" id="testimonials">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-tag">Témoignages</span>
                <h2 class="section-title">Ce qu'en disent<br>nos <span class="text-accent">utilisateurs</span></h2>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card reveal reveal-delay-1">
                    <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="testimonial-text">"ManageX a transformé notre gestion RH. Le pointage géolocalisé a éliminé les fraudes et l'assistant IA nous fait gagner 2h par jour sur l'analyse des données."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">AK</div>
                        <div>
                            <div class="testimonial-name">Aminata Koné</div>
                            <div class="testimonial-role">DRH — Groupe Solaris</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card reveal reveal-delay-2">
                    <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="testimonial-text">"La paie multi-pays est un game changer. Nous opérons en Côte d'Ivoire et au Sénégal, les fiches de paie sont générées automatiquement avec les bons barèmes."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">MD</div>
                        <div>
                            <div class="testimonial-name">Marc Diallo</div>
                            <div class="testimonial-role">CEO — TechBridge Africa</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card reveal reveal-delay-3">
                    <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="testimonial-text">"En tant qu'employée, j'adore la simplicité. Je pointe en un clic, je vois mes congés, et le chatbot IA répond instantanément à toutes mes questions RH."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">FB</div>
                        <div>
                            <div class="testimonial-name">Fatou Bamba</div>
                            <div class="testimonial-role">Développeuse — NovaTech</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== CTA ==================== -->
    <section class="cta-section section-pad" id="contact">
        <div class="cta-orb"></div>
        <div class="container">
            <div class="cta-inner">
                <span class="section-tag reveal">Prêt à commencer ?</span>
                <h2 class="reveal reveal-delay-1">Modernisez votre gestion RH <span class="text-accent">dès aujourd'hui</span></h2>
                <p class="reveal reveal-delay-2">Rejoignez les entreprises qui ont choisi ManageX pour simplifier, automatiser et piloter leur capital humain avec intelligence.</p>

                <form class="cta-form reveal reveal-delay-3" id="ctaForm">
                    <input type="email" placeholder="Votre email professionnel" required>
                    <button type="submit" class="btn-primary"><span>Demander une démo</span></button>
                </form>
                <div class="cta-success" id="ctaSuccess">
                    &#10003; &nbsp;Merci ! Notre équipe vous contactera sous 24h pour planifier votre démonstration.
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== FOOTER ==================== -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="<?php echo e(url('/')); ?>" class="nav-logo">
                        <div class="logo-icon">M</div>
                        ManageX
                    </a>
                    <p>La plateforme RH nouvelle génération propulsée par l'IA. Conçue pour les entreprises ambitieuses d'Afrique et du monde.</p>
                </div>
                <div class="footer-col">
                    <h4>Produit</h4>
                    <ul>
                        <li><a href="#features">Fonctionnalités</a></li>
                        <li><a href="#ai">Intelligence IA</a></li>
                        <li><a href="#">Tarifs</a></li>
                        <li><a href="#">Changelog</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Ressources</h4>
                    <ul>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Guide utilisateur</a></li>
                        <li><a href="#">API</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Entreprise</h4>
                    <ul>
                        <li><a href="#">À propos</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#">Confidentialité</a></li>
                        <li><a href="#">CGU</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-copy">&copy; <?php echo e(date('Y')); ?> ManageX. Tous droits réservés.</div>
                <div class="footer-socials">
                    <a href="#" class="footer-social-link" aria-label="LinkedIn">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6zM2 9h4v12H2zM4 2a2 2 0 110 4 2 2 0 010-4z"/></svg>
                    </a>
                    <a href="#" class="footer-social-link" aria-label="Twitter/X">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" class="footer-social-link" aria-label="GitHub">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ==================== JAVASCRIPT ==================== -->
    <script>
        // ========================================
        // THEME MANAGEMENT (Dark/Light Mode)
        // ========================================
        (function() {
            const html = document.documentElement;
            const themeToggle = document.getElementById('themeToggle');
            const STORAGE_KEY = 'managex-theme';
            
            // Get saved theme or detect system preference
            function getPreferredTheme() {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved) return saved;
                
                // Detect system preference
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    return 'dark';
                }
                return 'light';
            }
            
            // Apply theme
            function setTheme(theme) {
                html.setAttribute('data-theme', theme);
                localStorage.setItem(STORAGE_KEY, theme);
            }
            
            // Initialize theme on page load
            const initialTheme = getPreferredTheme();
            setTheme(initialTheme);
            
            // Toggle theme on button click
            themeToggle.addEventListener('click', () => {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            });
            
            // Listen for system preference changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                // Only auto-switch if user hasn't manually set a preference
                if (!localStorage.getItem(STORAGE_KEY)) {
                    setTheme(e.matches ? 'dark' : 'light');
                }
            });
        })();

        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

        const nav = document.getElementById('nav');
        let lastScroll = 0;
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 50);
            lastScroll = window.scrollY;
        }, { passive: true });

        const mobileToggle = document.getElementById('mobileToggle');
        const navLinks = document.getElementById('navLinks');
        mobileToggle.addEventListener('click', () => {
            mobileToggle.classList.toggle('active');
            navLinks.classList.toggle('open');
        });
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileToggle.classList.remove('active');
                navLinks.classList.remove('open');
            });
        });

        const parallaxBg = document.getElementById('parallaxBg');
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        parallaxBg.style.transform = `translateY(${window.scrollY * 0.3}px)`;
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
        }

        const chartBarsEl = document.getElementById('chartBars');
        const barHeights = [45, 60, 55, 70, 65, 80, 75, 85, 90, 70, 60, 80, 95, 75, 85, 90, 65, 70, 80, 85, 60, 75, 90, 70, 80, 85, 95, 70, 75, 80];
        barHeights.forEach((h, i) => {
            const bar = document.createElement('div');
            bar.classList.add('chart-bar');
            bar.style.height = '8px';
            bar.style.transitionDelay = `${i * 30}ms`;
            chartBarsEl.appendChild(bar);
        });
        const chartObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.querySelectorAll('.chart-bar').forEach((bar, i) => {
                        setTimeout(() => { bar.style.height = `${barHeights[i]}%`; }, 100);
                    });
                    chartObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });
        chartObserver.observe(chartBarsEl);

        const ctaForm = document.getElementById('ctaForm');
        const ctaSuccess = document.getElementById('ctaSuccess');
        ctaForm.addEventListener('submit', (e) => {
            e.preventDefault();
            ctaForm.style.display = 'none';
            ctaSuccess.classList.add('show');
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    </script>
</body>
</html>
<?php /**PATH D:\ManageX\resources\views/landing.blade.php ENDPATH**/ ?>