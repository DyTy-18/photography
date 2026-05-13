<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MarKual — Photography & Film</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:300,400,500&family=cormorant-garamond:300,300i,400,400i,500,500i,600,600i" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ── PALETA: Tierra volcánica + puna boliviana ── */
        :root {
            --bg:           #080704;   /* negro volcánico profundo   */
            --cream:        #ede3cc;   /* arena caliza                */
            --terracotta:   #c4571f;   /* tierra de Salta/altiplano  */
            --ochre:        #d4903a;   /* luz del atardecer en la puna*/
            --sage:         #7a9468;   /* verde húmedo del Pando     */
            --muted:        #7a6550;   /* tierra seca                */
            --border:       #1e160c;   /* tierra oscura              */
            --smoke:        #2a2018;   /* ceniza volcánica           */
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            background: var(--bg);
            color: var(--cream);
            font-family: 'Instrument Sans', sans-serif;
            cursor: none;
            overflow-x: hidden;
        }

        /* ── GRAIN — polvo del altiplano ── */
        body::after {
            content: '';
            position: fixed; inset: 0;
            pointer-events: none; z-index: 9999; opacity: .055;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)'/%3E%3C/svg%3E");
            background-size: 180px 180px;
        }

        /* ── CURSOR ── */
        .cursor-dot, .cursor-ring {
            position: fixed; border-radius: 50%;
            pointer-events: none; z-index: 10000;
            transform: translate(-50%, -50%);
        }
        .cursor-dot  { width: 4px; height: 4px; background: var(--cream); }
        .cursor-ring {
            width: 32px; height: 32px;
            border: 1px solid rgba(237,227,204,.35);
            transition: width .22s ease, height .22s ease, border-color .22s ease;
        }
        body:has(a:hover) .cursor-ring,
        body:has(button:hover) .cursor-ring,
        body:has(.gallery-item:hover) .cursor-ring {
            width: 58px; height: 58px;
            border-color: var(--terracotta);
        }

        /* ── NAV ── */
        nav.main-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 200;
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.6rem 2.5rem;
            transition: background .5s, backdrop-filter .5s;
        }
        nav.main-nav.scrolled {
            background: rgba(8,7,4,.88);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
        }
        .nav-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.25rem; font-weight: 400; letter-spacing: .1em;
            color: var(--cream); text-decoration: none;
        }
        .nav-links { display: flex; gap: 2.5rem; }
        .nav-links a {
            font-size: .6rem; letter-spacing: .22em; text-transform: uppercase;
            color: rgba(237,227,204,.45); text-decoration: none;
            transition: color .3s;
        }
        .nav-links a:hover { color: var(--cream); }

        /* ── HERO — vastedad del altiplano ── */
        .hero { position: relative; height: 100vh; overflow: hidden; }
        .hero-img {
            position: absolute; inset: 0; width: 100%; height: 100%;
            object-fit: cover; transform: scale(1.06);
            transition: transform 9s cubic-bezier(.2,.0,.4,1);
        }
        .hero.loaded .hero-img { transform: scale(1); }

        /* Gradiente cálido — como la luz de la puna al atardecer */
        .hero-overlay {
            position: absolute; inset: 0;
            background:
                linear-gradient(to top,  rgba(8,7,4,.95) 0%, transparent 55%),
                linear-gradient(to right, rgba(8,7,4,.4)  0%, transparent 60%),
                linear-gradient(160deg, rgba(196,87,31,.12) 0%, transparent 50%);
        }

        .hero-content {
            position: absolute; inset: 0;
            display: flex; flex-direction: column; justify-content: flex-end;
            padding: 3.5rem 2.5rem;
        }
        .hero-eyebrow {
            font-size: .58rem; letter-spacing: .28em; text-transform: uppercase;
            color: var(--terracotta); margin-bottom: 1.2rem;
            display: flex; align-items: center; gap: 1rem;
        }
        .hero-eyebrow::before {
            content: ''; width: 28px; height: 1px; background: var(--terracotta);
        }
        .hero-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(3.2rem, 8.5vw, 8rem);
            font-weight: 300; line-height: 1.02;
            color: var(--cream); max-width: 14ch;
        }
        .hero-title .accent { color: var(--terracotta); font-style: italic; }
        .hero-meta {
            margin-top: 2rem;
            display: flex; align-items: center;
            gap: 2rem; flex-wrap: wrap;
        }
        .hero-meta span {
            font-size: .58rem; letter-spacing: .22em; text-transform: uppercase;
            color: rgba(237,227,204,.35);
        }
        .hero-meta .dot { color: var(--terracotta); }

        /* Línea de horizonte — referencia visual al altiplano */
        .hero-horizon {
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 1px; background: linear-gradient(to right, var(--terracotta), transparent 60%);
            opacity: .4;
        }
        .hero-scroll-hint {
            position: absolute; right: 2.5rem; bottom: 3rem;
            display: flex; flex-direction: column; align-items: center; gap: .6rem;
        }
        .hero-scroll-hint span {
            writing-mode: vertical-rl;
            font-size: .52rem; letter-spacing: .22em; text-transform: uppercase;
            color: rgba(237,227,204,.3);
        }
        .scroll-bar {
            width: 1px; height: 48px;
            background: linear-gradient(to bottom, rgba(237,227,204,.3), transparent);
            animation: scrollPulse 2.2s ease-in-out infinite;
        }
        @keyframes scrollPulse {
            0%,100% { opacity: .3; transform: scaleY(1); }
            50%      { opacity: .7; transform: scaleY(1.3); }
        }

        /* ── QUOTE — cita del viaje ── */
        .quote-section {
            padding: 8rem 2.5rem 7rem;
            position: relative; overflow: hidden;
        }
        /* Topografía sutil de fondo */
        .quote-section::before {
            content: '';
            position: absolute; inset: 0; pointer-events: none;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 39px,
                rgba(196,87,31,.04) 39px,
                rgba(196,87,31,.04) 40px
            );
        }
        .quote-inner {
            max-width: 860px; margin: 0 auto; position: relative;
        }
        .quote-glyph {
            font-family: 'Cormorant Garamond', serif;
            font-size: 12rem; line-height: .5;
            color: var(--terracotta); opacity: .12;
            position: absolute; top: -1rem; left: -2rem;
            pointer-events: none; user-select: none;
        }
        .quote-year-tag {
            font-size: .58rem; letter-spacing: .3em; text-transform: uppercase;
            color: var(--terracotta); margin-bottom: 2rem;
            display: flex; align-items: center; gap: 1rem;
        }
        .quote-year-tag::after {
            content: ''; flex: 1; max-width: 60px; height: 1px;
            background: var(--terracotta); opacity: .5;
        }
        .quote-body {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.8rem, 4vw, 3.5rem);
            font-weight: 300; font-style: italic;
            line-height: 1.35; color: var(--cream);
        }
        .quote-body strong {
            font-style: normal; font-weight: 600;
            color: var(--terracotta);
        }

        /* ── LOCATIONS INDEX ── */
        .locations-section {
            padding: 0 2.5rem 6rem;
            border-top: 1px solid var(--border);
        }
        .locations-header {
            padding: 2.5rem 0 2.5rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .section-label {
            font-size: .58rem; letter-spacing: .25em; text-transform: uppercase;
            color: var(--muted); display: flex; align-items: center; gap: 1rem;
        }
        .section-label::after {
            content: ''; width: 40px; height: 1px; background: var(--border);
        }
        .section-number { color: var(--terracotta); margin-right: .2rem; }
        .locations-count {
            font-size: .58rem; letter-spacing: .2em; text-transform: uppercase;
            color: rgba(237,227,204,.2);
        }
        .locations-list { list-style: none; }
        .location-row {
            display: grid;
            grid-template-columns: 3rem 1fr auto auto;
            align-items: baseline; gap: 1.5rem;
            padding: 1.15rem 0;
            border-bottom: 1px solid var(--border);
            transition: padding-left .28s ease;
            cursor: none;
        }
        .location-row:hover { padding-left: .8rem; }
        .location-row:hover .loc-num  { color: var(--terracotta); }
        .location-row:hover .loc-name { color: var(--cream); }
        /* Volcanes con acento diferente */
        .location-row.volcan:hover .loc-num  { color: var(--ochre); }
        .location-row.selva:hover .loc-num   { color: var(--sage); }
        .loc-num {
            font-family: 'Cormorant Garamond', serif;
            font-size: .85rem; color: var(--smoke);
            transition: color .25s;
        }
        .loc-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1rem, 2vw, 1.65rem);
            font-weight: 400; color: rgba(237,227,204,.65);
            transition: color .25s;
        }
        .loc-alt {
            font-size: .52rem; letter-spacing: .15em; text-transform: uppercase;
            color: var(--ochre); opacity: .7; white-space: nowrap;
        }
        .loc-region {
            font-size: .52rem; letter-spacing: .18em; text-transform: uppercase;
            color: var(--muted); white-space: nowrap; text-align: right;
        }
        @media (max-width: 640px) {
            .location-row { grid-template-columns: 2.5rem 1fr; }
            .loc-alt, .loc-region { display: none; }
        }

        /* ── GALLERY ── */
        .gallery-section { padding: 5rem 0; }
        .gallery-header { padding: 0 2.5rem 3rem; }
        .filters { display: flex; gap: .4rem; flex-wrap: wrap; margin-top: 2rem; }
        .filter-btn {
            padding: .38rem 1rem;
            font-size: .58rem; letter-spacing: .18em; text-transform: uppercase;
            border: 1px solid var(--border);
            background: transparent; color: var(--muted);
            border-radius: 999px; cursor: none; transition: all .25s;
        }
        .filter-btn:hover   { color: var(--cream); border-color: var(--smoke); }
        .filter-btn.active  {
            background: var(--terracotta);
            color: var(--cream); border-color: var(--terracotta);
        }
        .filter-btn[data-filter="bolivia"].active  { background: var(--terracotta); }
        .filter-btn[data-filter="volcanes"].active { background: var(--ochre); color: var(--bg); }
        .filter-btn[data-filter="selva"].active    { background: var(--sage); color: var(--bg); }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 2px;
        }
        .gallery-item {
            overflow: hidden; position: relative; cursor: none;
            opacity: 0; transform: translateY(24px);
            transition: opacity .8s ease, transform .8s ease;
        }
        .gallery-item.visible { opacity: 1; transform: translateY(0); }
        .gallery-item.hidden-item { display: none; }
        .gallery-item img {
            width: 100%; height: 100%; object-fit: cover; display: block;
            transition: transform .9s cubic-bezier(.25,.46,.45,.94), filter .5s;
            filter: saturate(.88);
        }
        .gallery-item:hover img { transform: scale(1.05); filter: saturate(1.05); }

        .item-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(8,7,4,.82) 0%, transparent 55%);
            opacity: 0; transition: opacity .4s;
            display: flex; align-items: flex-end; padding: 1.5rem;
        }
        .gallery-item:hover .item-overlay { opacity: 1; }
        .item-info { transform: translateY(6px); transition: transform .35s; }
        .gallery-item:hover .item-info { transform: translateY(0); }
        .item-cat {
            font-size: .5rem; letter-spacing: .22em; text-transform: uppercase;
            margin-bottom: .3rem;
        }
        .item-cat.tc  { color: var(--terracotta); }
        .item-cat.oc  { color: var(--ochre); }
        .item-cat.sg  { color: var(--sage); }
        .item-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem; color: var(--cream);
        }

        /* video badge */
        .video-badge {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 52px; height: 52px; border-radius: 50%;
            border: 1.5px solid rgba(237,227,204,.65);
            display: flex; align-items: center; justify-content: center;
            background: rgba(8,7,4,.35);
            transition: transform .3s, border-color .3s;
        }
        .gallery-item:hover .video-badge {
            transform: translate(-50%, -50%) scale(1.15);
            border-color: var(--ochre);
        }
        .video-badge svg { margin-left: 3px; }

        /* grid helpers */
        .col-12 { grid-column: span 12; }
        .col-8  { grid-column: span 8;  }
        .col-7  { grid-column: span 7;  }
        .col-6  { grid-column: span 6;  }
        .col-5  { grid-column: span 5;  }
        .col-4  { grid-column: span 4;  }
        .h-hero  { height: 72vh; }
        .h-tall  { height: 82vh; }
        .h-mid   { height: 56vh; }
        .h-short { height: 44vh; }
        @media (max-width: 768px) {
            .col-8,.col-7,.col-6,.col-5,.col-4 { grid-column: span 12; }
            .h-tall,.h-mid,.h-short { height: 60vw; }
            .h-hero { height: 70vw; }
        }

        /* ── ABOUT ── */
        .about-section {
            display: grid; grid-template-columns: 1fr 1fr;
            min-height: 88vh; border-top: 1px solid var(--border);
        }
        .about-photo { overflow: hidden; }
        .about-photo img { width: 100%; height: 100%; object-fit: cover; filter: saturate(.9); }
        .about-text {
            display: flex; flex-direction: column; justify-content: center;
            padding: 5rem 4rem; background: var(--smoke);
        }
        .about-quote {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.5rem, 2.3vw, 2.4rem);
            font-weight: 300; line-height: 1.45;
            color: rgba(237,227,204,.85);
            margin: 2rem 0;
        }
        .about-quote em { font-style: italic; color: var(--terracotta); }
        .about-bio {
            font-size: .78rem; line-height: 1.9;
            color: var(--muted); max-width: 40ch; margin-bottom: 2.5rem;
        }
        .cta-link {
            display: inline-flex; align-items: center; gap: .75rem;
            font-size: .58rem; letter-spacing: .22em; text-transform: uppercase;
            color: var(--cream); text-decoration: none; transition: gap .3s;
        }
        .cta-link::after {
            content: ''; width: 28px; height: 1px; background: var(--terracotta);
            transition: width .3s;
        }
        .cta-link:hover { gap: 1.1rem; }
        .cta-link:hover::after { width: 44px; }
        @media (max-width: 768px) {
            .about-section { grid-template-columns: 1fr; }
            .about-photo { height: 55vw; }
            .about-text  { padding: 3rem 1.5rem; }
        }

        /* ── CONTACT ── */
        .contact-section {
            min-height: 85vh; display: flex; flex-direction: column;
            justify-content: center; padding: 6rem 2.5rem;
            border-top: 1px solid var(--border);
            position: relative; overflow: hidden;
        }
        /* Círculo de calor — sol del altiplano */
        .contact-section::before {
            content: '';
            position: absolute; top: -20%; right: -10%;
            width: 60vw; height: 60vw; border-radius: 50%;
            background: radial-gradient(circle, rgba(196,87,31,.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .contact-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(4rem, 11vw, 10rem);
            font-weight: 300; line-height: .95;
            color: var(--cream); margin: 2rem 0 3.5rem; position: relative;
        }
        .contact-title span { color: var(--terracotta); }
        .contact-row {
            display: flex; align-items: flex-end;
            justify-content: space-between; flex-wrap: wrap; gap: 2rem;
            border-top: 1px solid var(--border); padding-top: 2rem;
            position: relative;
        }
        .contact-email {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem; font-weight: 300;
            color: rgba(237,227,204,.55); text-decoration: none;
            transition: color .3s;
        }
        .contact-email:hover { color: var(--cream); }
        .social-links { display: flex; gap: 2rem; }
        .social-links a {
            font-size: .58rem; letter-spacing: .2em; text-transform: uppercase;
            color: var(--muted); text-decoration: none; transition: color .3s;
        }
        .social-links a:hover { color: var(--terracotta); }

        /* ── FOOTER ── */
        footer {
            padding: 1.5rem 2.5rem;
            display: flex; justify-content: space-between; align-items: center;
            border-top: 1px solid var(--border);
        }
        footer span { font-size: .52rem; letter-spacing: .2em; text-transform: uppercase; color: var(--smoke); }

        /* ── LIGHTBOX ── */
        .lightbox {
            position: fixed; inset: 0; z-index: 2000;
            background: rgba(8,7,4,.97);
            display: none; align-items: center; justify-content: center;
        }
        .lightbox.open { display: flex; }
        .lightbox-img {
            max-width: 90vw; max-height: 88vh; object-fit: contain;
            animation: lbIn .3s ease;
        }
        @keyframes lbIn { from { opacity:0; transform:scale(.96); } to { opacity:1; transform:scale(1); } }
        .lb-close {
            position: absolute; top: 1.5rem; right: 2rem;
            font-size: .58rem; letter-spacing: .2em; text-transform: uppercase;
            color: rgba(237,227,204,.35); cursor: none; transition: color .3s;
            background: none; border: none;
        }
        .lb-close:hover { color: var(--cream); }
        .lb-prev, .lb-next {
            position: absolute; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: none;
            color: rgba(237,227,204,.3); padding: 1.5rem; transition: color .3s;
            font-size: 1.2rem;
        }
        .lb-prev:hover, .lb-next:hover { color: var(--cream); }
        .lb-prev { left: 1rem; } .lb-next { right: 1rem; }
        .lb-caption {
            position: absolute; bottom: 2rem; left: 50%;
            transform: translateX(-50%); text-align: center;
        }
        .lb-caption p {
            font-size: .55rem; letter-spacing: .22em; text-transform: uppercase;
            color: rgba(237,227,204,.3);
        }

        /* ── VIDEO MODAL ── */
        .video-modal {
            position: fixed; inset: 0; z-index: 2000;
            background: rgba(8,7,4,.96);
            display: none; align-items: center; justify-content: center; padding: 2rem;
        }
        .video-modal.open { display: flex; }
        .video-wrap { width: 100%; max-width: 960px; aspect-ratio: 16/9; position: relative; }
        .video-wrap iframe { width: 100%; height: 100%; border: none; }
        .vm-close {
            position: absolute; top: -2.5rem; right: 0;
            font-size: .58rem; letter-spacing: .2em; text-transform: uppercase;
            color: rgba(237,227,204,.35); cursor: none; transition: color .3s;
            background: none; border: none;
        }
        .vm-close:hover { color: var(--cream); }

        /* ── REVEAL ── */
        .reveal {
            opacity: 0; transform: translateY(22px);
            transition: opacity .9s ease, transform .9s ease;
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body>

    <div class="cursor-dot"  id="cursorDot"></div>
    <div class="cursor-ring" id="cursorRing"></div>

    <!-- NAV -->
    <nav class="main-nav" id="mainNav">
        <a href="#" class="nav-logo">MarKual</a>
        <div class="nav-links">
            <a href="#gallery">Work</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero" id="hero">
        <img class="hero-img"
             src="https://picsum.photos/seed/volcan-altiplano-hero/1920/1080"
             alt="Hero"
             onload="this.closest('.hero').classList.add('loaded')">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <p class="hero-eyebrow">2025 — Photography & Film</p>
            <h1 class="hero-title">
                Caminos que<br>
                <span class="accent">ni sabía</span><br>
                que existían.
            </h1>
            <div class="hero-meta">
                <span>Bolivia</span>
                <span class="dot">·</span>
                <span>Argentina</span>
                <span class="dot">·</span>
                <span>Chile</span>
            </div>
        </div>
        <div class="hero-horizon"></div>
        <div class="hero-scroll-hint">
            <div class="scroll-bar"></div>
            <span>Scroll</span>
        </div>
    </section>

    <!-- QUOTE -->
    {{-- <section class="quote-section">
        <div class="quote-inner reveal">
            <span class="quote-glyph">"</span>
            <p class="quote-year-tag">2025</p>
            <p class="quote-body">
                "El 2025 me mostró<br>
                <strong>caminos</strong> que ni sabía<br>
                que existían."
            </p>
        </div>
    </section> --}}

    <!-- LOCATIONS INDEX -->
    {{-- <section class="locations-section">
        <div class="locations-header">
            <div class="section-label">
                <span class="section-number">01</span> Destinos
            </div>
            <span class="locations-count">09 lugares · 2025</span>
        </div>
        <ul class="locations-list">
            <li class="location-row volcan reveal">
                <span class="loc-num">01</span>
                <span class="loc-name">Volcán Llullailaco</span>
                <span class="loc-alt">6.739 m</span>
                <span class="loc-region">Frontera Argentina–Chile</span>
            </li>
            <li class="location-row reveal">
                <span class="loc-num">02</span>
                <span class="loc-name">San Antonio de Los Cobres</span>
                <span class="loc-alt">3.775 m</span>
                <span class="loc-region">Salta, Argentina</span>
            </li>
            <li class="location-row reveal">
                <span class="loc-num">03</span>
                <span class="loc-name">Salta</span>
                <span class="loc-alt">1.187 m</span>
                <span class="loc-region">Argentina</span>
            </li>
            <li class="location-row reveal">
                <span class="loc-num">04</span>
                <span class="loc-name">Jatun Q'asa</span>
                <span class="loc-alt">~4.500 m</span>
                <span class="loc-region">Cochabamba, Bolivia</span>
            </li>
            <li class="location-row reveal">
                <span class="loc-num">05</span>
                <span class="loc-name">Serkhe Koullo</span>
                <span class="loc-alt">~5.100 m</span>
                <span class="loc-region">La Paz, Bolivia</span>
            </li>
            <li class="location-row selva reveal">
                <span class="loc-num">06</span>
                <span class="loc-name">Jericó</span>
                <span class="loc-alt">~200 m</span>
                <span class="loc-region">Pando, Bolivia</span>
            </li>
            <li class="location-row selva reveal">
                <span class="loc-num">07</span>
                <span class="loc-name">Puerto Rico</span>
                <span class="loc-alt">~200 m</span>
                <span class="loc-region">Pando, Bolivia</span>
            </li>
            <li class="location-row volcan reveal">
                <span class="loc-num">08</span>
                <span class="loc-name">Volcán Uturuncu</span>
                <span class="loc-alt">6.008 m</span>
                <span class="loc-region">Potosí, Bolivia</span>
            </li>
            <li class="location-row volcan reveal">
                <span class="loc-num">09</span>
                <span class="loc-name">Volcán Licancabur</span>
                <span class="loc-alt">5.916 m</span>
                <span class="loc-region">Frontera Bolivia–Chile</span>
            </li>
        </ul>
    </section> --}}

    <!-- GALLERY -->
    <section class="gallery-section" id="gallery">
        <div class="gallery-header">
            <div class="section-label">
                <span class="section-number">01</span> Galería
            </div>
            <div class="filters" id="filters">
                <button class="filter-btn active" data-filter="all">Todo</button>
                <button class="filter-btn" data-filter="volcanes">Volcanes</button>
                <button class="filter-btn" data-filter="bolivia">Bolivia</button>
                <button class="filter-btn" data-filter="argentina">Argentina</button>
                <button class="filter-btn" data-filter="selva">Selva</button>
                <button class="filter-btn" data-filter="video">Video</button>
            </div>
        </div>

        <div class="gallery-grid" id="galleryGrid">

            <!-- 01 Llullailaco — full, el volcán abre la galería -->
            <div class="gallery-item col-12 h-hero" data-cat="volcanes"
                 data-src="https://picsum.photos/seed/llullailaco-6739/1600/900"
                 data-title="Volcán Llullailaco" data-category="Volcanes · 6.739 m">
                <img src="https://picsum.photos/seed/llullailaco-6739/1600/900" alt="Volcán Llullailaco" loading="lazy">
                <div class="item-overlay">
                    <div class="item-info">
                        <p class="item-cat oc">Volcanes — 6.739 m</p>
                        <p class="item-title">Volcán Llullailaco</p>
                    </div>
                </div>
            </div>

            <!-- 02 San Antonio (7) + 03 Salta (5) -->
            <div class="gallery-item col-7 h-tall" data-cat="argentina"
                 data-src="https://picsum.photos/seed/san-antonio-cobres/900/1200"
                 data-title="San Antonio de Los Cobres" data-category="Argentina · Salta">
                <img src="https://picsum.photos/seed/san-antonio-cobres/900/1200" alt="San Antonio de Los Cobres" loading="lazy">
                <div class="item-overlay">
                    <div class="item-info">
                        <p class="item-cat tc">Argentina — Salta</p>
                        <p class="item-title">San Antonio de Los Cobres</p>
                    </div>
                </div>
            </div>

            <div class="gallery-item col-5 h-tall" data-cat="argentina"
                 data-src="https://picsum.photos/seed/salta-argentina/700/1200"
                 data-title="Salta" data-category="Argentina">
                <img src="https://picsum.photos/seed/salta-argentina/700/1200" alt="Salta" loading="lazy">
                <div class="item-overlay">
                    <div class="item-info">
                        <p class="item-cat tc">Argentina</p>
                        <p class="item-title">Salta</p>
                    </div>
                </div>
            </div>

            <!-- Video — el viaje, full -->
            <div class="gallery-item col-12 h-hero" data-cat="video"
                 data-video="https://www.youtube.com/embed/dQw4w9WgXcQ"
                 data-title="2025 — El viaje" data-category="Video">
                <img src="https://picsum.photos/seed/video-2025-viaje/1600/900" alt="Video" loading="lazy">
                <div class="item-overlay" style="opacity:1; background: linear-gradient(to top, rgba(8,7,4,.7) 0%, transparent 50%);">
                    <div class="item-info">
                        <p class="item-cat oc">Video — 2025</p>
                        <p class="item-title">El viaje</p>
                    </div>
                </div>
                <div class="video-badge">
                    <svg width="15" height="15" fill="white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
            </div>

            <!-- 04 Jatun Q'asa (7) + 05 Serkhe Koullo (5) -->
            <div class="gallery-item col-7 h-mid" data-cat="bolivia"
                 data-src="https://picsum.photos/seed/jatun-qasa-cbba/1100/800"
                 data-title="Jatun Q'asa" data-category="Bolivia · Cochabamba">
                <img src="https://picsum.photos/seed/jatun-qasa-cbba/1100/800" alt="Jatun Q'asa" loading="lazy">
                <div class="item-overlay">
                    <div class="item-info">
                        <p class="item-cat tc">Bolivia — Cochabamba</p>
                        <p class="item-title">Jatun Q'asa</p>
                    </div>
                </div>
            </div>

            <div class="gallery-item col-5 h-mid" data-cat="bolivia"
                 data-src="https://picsum.photos/seed/serkhe-koullo-lpz/700/800"
                 data-title="Serkhe Koullo" data-category="Bolivia · La Paz">
                <img src="https://picsum.photos/seed/serkhe-koullo-lpz/700/800" alt="Serkhe Koullo" loading="lazy">
                <div class="item-overlay">
                    <div class="item-info">
                        <p class="item-cat tc">Bolivia — La Paz</p>
                        <p class="item-title">Serkhe Koullo</p>
                    </div>
                </div>
            </div>

            <!-- 06 Jericó (4) + 07 Puerto Rico (4) + Video Pando (4) -->
            <div class="gallery-item col-4 h-tall" data-cat="selva"
                 data-src="https://picsum.photos/seed/jerico-pando-bv/600/950"
                 data-title="Jericó" data-category="Selva · Pando">
                <img src="https://picsum.photos/seed/jerico-pando-bv/600/950" alt="Jericó, Pando" loading="lazy">
                <div class="item-overlay">
                    <div class="item-info">
                        <p class="item-cat sg">Selva — Pando</p>
                        <p class="item-title">Jericó</p>
                    </div>
                </div>
            </div>

            <div class="gallery-item col-4 h-tall" data-cat="selva"
                 data-src="https://picsum.photos/seed/puerto-rico-pando-bv/600/950"
                 data-title="Puerto Rico" data-category="Selva · Pando">
                <img src="https://picsum.photos/seed/puerto-rico-pando-bv/600/950" alt="Puerto Rico, Pando" loading="lazy">
                <div class="item-overlay">
                    <div class="item-info">
                        <p class="item-cat sg">Selva — Pando</p>
                        <p class="item-title">Puerto Rico</p>
                    </div>
                </div>
            </div>

            <div class="gallery-item col-4 h-tall" data-cat="video"
                 data-video="https://www.youtube.com/embed/dQw4w9WgXcQ"
                 data-title="Pando — Amazonía" data-category="Video · Selva">
                <img src="https://picsum.photos/seed/video-amazonia-pando/600/950" alt="Video Amazonía" loading="lazy">
                <div class="item-overlay" style="opacity:1; background: linear-gradient(to top, rgba(8,7,4,.65) 0%, transparent 55%);">
                    <div class="item-info">
                        <p class="item-cat sg">Video — Pando</p>
                        <p class="item-title">Amazonía</p>
                    </div>
                </div>
                <div class="video-badge">
                    <svg width="15" height="15" fill="white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
            </div>

            <!-- 08 Uturuncu (8) + detalle Potosí (4) -->
            <div class="gallery-item col-8 h-mid" data-cat="volcanes"
                 data-src="https://picsum.photos/seed/uturuncu-6008/1200/800"
                 data-title="Volcán Uturuncu" data-category="Volcanes · 6.008 m">
                <img src="https://picsum.photos/seed/uturuncu-6008/1200/800" alt="Volcán Uturuncu" loading="lazy">
                <div class="item-overlay">
                    <div class="item-info">
                        <p class="item-cat oc">Volcanes — 6.008 m</p>
                        <p class="item-title">Volcán Uturuncu</p>
                    </div>
                </div>
            </div>

            <div class="gallery-item col-4 h-mid" data-cat="bolivia"
                 data-src="https://picsum.photos/seed/altiplano-potosi/600/800"
                 data-title="Altiplano" data-category="Bolivia · Potosí">
                <img src="https://picsum.photos/seed/altiplano-potosi/600/800" alt="Altiplano, Potosí" loading="lazy">
                <div class="item-overlay">
                    <div class="item-info">
                        <p class="item-cat tc">Bolivia — Potosí</p>
                        <p class="item-title">Altiplano</p>
                    </div>
                </div>
            </div>

            <!-- 09 Licancabur — full, cierre épico -->
            <div class="gallery-item col-12 h-hero" data-cat="volcanes"
                 data-src="https://picsum.photos/seed/licancabur-5916/1600/900"
                 data-title="Volcán Licancabur" data-category="Volcanes · 5.916 m">
                <img src="https://picsum.photos/seed/licancabur-5916/1600/900" alt="Volcán Licancabur" loading="lazy">
                <div class="item-overlay">
                    <div class="item-info">
                        <p class="item-cat oc">Volcanes — 5.916 m</p>
                        <p class="item-title">Volcán Licancabur</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ABOUT -->
    <section class="about-section" id="about">
        <div class="about-photo">
            <img src="https://picsum.photos/seed/marco-portrait-bv/800/1100" alt="Marco">
        </div>
        <div class="about-text">
            <div class="section-label" style="max-width:280px">
                <span class="section-number">02</span> About
            </div>
            <p class="about-quote">
                Ingeniero civil boliviano<br>
                que <em>no para de viajar.</em>
            </p>
            <p class="about-bio">
                MarKual es boliviano, ingeniero civil de profesión y viajero de vocación. Entre volcanes, selvas y altiplanos, captura los paisajes que la mayoría no llega a ver — a pie, en bote, en helicóptero o caminando sin rumbo fijo. Amante de las montañas y de los caminos que ni sabía que existían.
            </p>
            <a href="#contact" class="cta-link">Trabajemos juntos</a>
        </div>
    </section>

    <!-- CONTACT -->
    <section class="contact-section" id="contact">
        <div class="section-label" style="max-width:280px">
            <span class="section-number">03</span> Contact
        </div>
        <h2 class="contact-title">
            Hable<span>mos</span><br>
            de tu<br>proyecto.
        </h2>
        <div class="contact-row">
            <a href="mailto:marco@example.com" class="contact-email">marco@example.com</a>
            <div class="social-links">
                <a href="https://www.instagram.com/markualko" target="_blank">Instagram</a>
                <a href="#">YouTube</a>
                <a href="#">Vimeo</a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <span>© {{ date('Y') }} MarKual</span>
        <span>Photography & Film · Bolivia · Argentina · Chile</span>
    </footer>

    <!-- LIGHTBOX -->
    <div class="lightbox" id="lightbox">
        <button class="lb-close" id="lbClose">Cerrar ✕</button>
        <button class="lb-prev" id="lbPrev">&#8592;</button>
        <img class="lightbox-img" id="lbImg" src="" alt="">
        <button class="lb-next" id="lbNext">&#8594;</button>
        <div class="lb-caption"><p id="lbCaption"></p></div>
    </div>

    <!-- VIDEO MODAL -->
    <div class="video-modal" id="videoModal">
        <div class="video-wrap">
            <button class="vm-close" id="vmClose">Cerrar ✕</button>
            <iframe id="videoFrame" src="" allowfullscreen></iframe>
        </div>
    </div>

    <script>
    // ── CURSOR ──────────────────────────────
    const dot = document.getElementById('cursorDot');
    const ring = document.getElementById('cursorRing');
    let mx=0,my=0,rx=0,ry=0;
    document.addEventListener('mousemove', e=>{ mx=e.clientX; my=e.clientY; });
    (function tick(){
        rx += (mx-rx)*.11; ry += (my-ry)*.11;
        dot.style.cssText  = `left:${mx}px;top:${my}px`;
        ring.style.cssText = `left:${rx}px;top:${ry}px`;
        requestAnimationFrame(tick);
    })();

    // ── NAV ─────────────────────────────────
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', ()=> nav.classList.toggle('scrolled', scrollY > 60));

    // ── REVEAL on scroll ────────────────────
    const revObs = new IntersectionObserver(entries=>{
        entries.forEach((e,i)=>{
            if(e.isIntersecting){
                setTimeout(()=> e.target.classList.add('visible'), i*70);
                revObs.unobserve(e.target);
            }
        });
    }, { threshold:.07 });
    document.querySelectorAll('.reveal, .gallery-item').forEach(el=> revObs.observe(el));

    // ── FILTERS ─────────────────────────────
    document.querySelectorAll('.filter-btn').forEach(btn=>{
        btn.addEventListener('click', ()=>{
            document.querySelectorAll('.filter-btn').forEach(b=> b.classList.remove('active'));
            btn.classList.add('active');
            const f = btn.dataset.filter;
            document.querySelectorAll('.gallery-item').forEach(item=>{
                const show = f==='all' || item.dataset.cat===f;
                item.classList.toggle('hidden-item', !show);
            });
        });
    });

    // ── LIGHTBOX ────────────────────────────
    const photoItems = ()=> [...document.querySelectorAll('.gallery-item[data-src]:not(.hidden-item)')];
    let lbIdx = 0;
    const lightbox = document.getElementById('lightbox');
    const lbImg    = document.getElementById('lbImg');
    const lbCap    = document.getElementById('lbCaption');

    function showLb(idx){
        const items = photoItems(); if(!items.length) return;
        lbIdx = (idx+items.length)%items.length;
        const el = items[lbIdx];
        lbImg.src = el.dataset.src;
        lbCap.textContent = el.dataset.category+' — '+el.dataset.title;
    }
    function closeLb(){ lightbox.classList.remove('open'); document.body.style.overflow=''; }

    document.querySelectorAll('.gallery-item[data-src]').forEach(el=>{
        el.addEventListener('click', ()=>{
            lbIdx = photoItems().indexOf(el);
            showLb(lbIdx);
            lightbox.classList.add('open');
            document.body.style.overflow='hidden';
        });
    });
    document.getElementById('lbClose').addEventListener('click', closeLb);
    document.getElementById('lbPrev').addEventListener('click', ()=> showLb(lbIdx-1));
    document.getElementById('lbNext').addEventListener('click', ()=> showLb(lbIdx+1));
    lightbox.addEventListener('click', e=>{ if(e.target===lightbox) closeLb(); });

    // ── VIDEO ────────────────────────────────
    const vm    = document.getElementById('videoModal');
    const vf    = document.getElementById('videoFrame');
    function closeVm(){ vf.src=''; vm.classList.remove('open'); document.body.style.overflow=''; }

    document.querySelectorAll('.gallery-item[data-video]').forEach(el=>{
        el.addEventListener('click', ()=>{
            vf.src = el.dataset.video;
            vm.classList.add('open');
            document.body.style.overflow='hidden';
        });
    });
    document.getElementById('vmClose').addEventListener('click', closeVm);
    vm.addEventListener('click', e=>{ if(e.target===vm) closeVm(); });

    // ── KEYBOARD ─────────────────────────────
    document.addEventListener('keydown', e=>{
        if(e.key==='Escape')     { closeLb(); closeVm(); }
        if(e.key==='ArrowRight') showLb(lbIdx+1);
        if(e.key==='ArrowLeft')  showLb(lbIdx-1);
    });
    </script>

</body>
</html>
