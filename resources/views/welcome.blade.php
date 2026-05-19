<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MarKual — Photography & Film</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:300,400,500&family=dm-serif-display:400,400i" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
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
            font-family: 'DM Sans', sans-serif;
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
            font-family: 'DM Serif Display', serif;
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
            font-family: 'DM Serif Display', serif;
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
            font-family: 'DM Serif Display', serif;
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
            font-family: 'DM Serif Display', serif;
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
            font-family: 'DM Serif Display', serif;
            font-size: .85rem; color: var(--smoke);
            transition: color .25s;
        }
        .loc-name {
            font-family: 'DM Serif Display', serif;
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
        .filter-btn[data-filter="alta-montana"].active { background: var(--ochre);      color: var(--bg); }
        .filter-btn[data-filter="trekking"].active     { background: var(--terracotta); }
        .filter-btn[data-filter="amazonia"].active     { background: var(--sage);       color: var(--bg); }
        .filter-btn[data-filter="altiplano"].active    { background: var(--terracotta); }
        .filter-btn[data-filter="cultura"].active      { background: var(--muted); }
        .filter-btn[data-filter="dron"].active         { background: var(--ochre);      color: var(--bg); }

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
            font-family: 'DM Serif Display', serif;
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
            font-family: 'DM Serif Display', serif;
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
            font-family: 'DM Serif Display', serif;
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
            font-family: 'DM Serif Display', serif;
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
            background: rgba(8,7,4,.98);
            display: none;
        }
        .lightbox.open { display: flex; }

        .lb-close {
            position: absolute; top: 1.5rem; right: 2rem; z-index: 10;
            font-size: .58rem; letter-spacing: .2em; text-transform: uppercase;
            color: rgba(237,227,204,.35); cursor: none; transition: color .3s;
            background: none; border: none;
        }
        .lb-close:hover { color: var(--cream); }

        /* left — photo */
        .lb-left {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 4rem 2rem 4rem 2rem;
            position: relative; min-width: 0;
        }
        .lightbox-img {
            max-width: 100%; max-height: 82vh; object-fit: contain;
            animation: lbIn .35s ease;
        }
        @keyframes lbIn { from { opacity:0; transform:scale(.97); } to { opacity:1; transform:scale(1); } }

        .lb-nav {
            display: flex; gap: 2rem; margin-top: 1.5rem;
        }
        .lb-prev, .lb-next {
            background: none; border: 1px solid var(--border); cursor: none;
            color: rgba(237,227,204,.35); padding: .6rem 1.4rem;
            font-size: 1rem; transition: color .25s, border-color .25s;
            letter-spacing: .1em;
        }
        .lb-prev:hover, .lb-next:hover { color: var(--cream); border-color: var(--muted); }

        /* right — info panel */
        .lb-right {
            width: 380px; flex-shrink: 0;
            background: var(--smoke);
            border-left: 1px solid var(--border);
            display: flex; flex-direction: column;
            overflow-y: auto;
        }

        .lb-panel-head {
            padding: 3rem 2rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        .lb-panel-eyebrow {
            font-size: .52rem; letter-spacing: .25em; text-transform: uppercase;
            color: var(--terracotta); margin-bottom: .8rem;
            display: flex; align-items: center; gap: .6rem;
        }
        .lb-panel-eyebrow .gps-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--terracotta);
            box-shadow: 0 0 0 0 rgba(196,87,31,.5);
            animation: gpsPulse 1.8s ease-out infinite;
        }
        @keyframes gpsPulse {
            0%  { box-shadow: 0 0 0 0 rgba(196,87,31,.5); }
            70% { box-shadow: 0 0 0 8px rgba(196,87,31,0); }
            100%{ box-shadow: 0 0 0 0 rgba(196,87,31,0); }
        }
        .lb-panel-name {
            font-family: 'DM Serif Display', serif;
            font-size: 1.6rem; line-height: 1.15;
            color: var(--cream); min-height: 2em;
        }
        .lb-cursor {
            display: inline-block; width: 2px; height: 1.1em;
            background: var(--terracotta); margin-left: 2px;
            vertical-align: text-bottom;
            animation: curBlink .7s step-end infinite;
        }
        @keyframes curBlink { 50% { opacity: 0; } }
        .lb-panel-meta {
            margin-top: .6rem;
            font-size: .52rem; letter-spacing: .18em; text-transform: uppercase;
            color: rgba(237,227,204,.3); display: flex; gap: 1rem; flex-wrap: wrap;
        }
        .lb-panel-meta .meta-alt { color: var(--ochre); }

        /* route SVG animation */
        .lb-route-wrap {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border);
        }
        .lb-route-label {
            font-size: .5rem; letter-spacing: .2em; text-transform: uppercase;
            color: var(--muted); margin-bottom: .8rem;
        }
        .lb-route-svg {
            width: 100%; height: 80px; overflow: visible;
        }
        .lb-route-terrain {
            stroke: rgba(196,87,31,.1); stroke-width: 1; fill: none;
        }
        .lb-route-path {
            stroke: var(--terracotta); stroke-width: 1.5; fill: none;
            stroke-linecap: round; stroke-linejoin: round;
            stroke-dasharray: 340;
            stroke-dashoffset: 340;
        }
        .lb-route-path.animate {
            animation: drawRoute 1.6s cubic-bezier(.4,0,.2,1) forwards;
        }
        @keyframes drawRoute { to { stroke-dashoffset: 0; } }
        .lb-route-dot {
            fill: var(--terracotta); opacity: 0;
        }
        .lb-route-dot.animate {
            animation: showDot .3s ease forwards 1.5s;
        }
        @keyframes showDot { to { opacity: 1; } }
        .lb-route-dot-pulse {
            fill: none; stroke: var(--terracotta); stroke-width: 1; opacity: 0;
        }
        .lb-route-dot-pulse.animate {
            animation: pulseDot 1.6s ease-out infinite 1.8s, showDot .1s ease forwards 1.5s;
        }
        @keyframes pulseDot {
            0%   { r: 5; opacity: .6; }
            100% { r: 14; opacity: 0; }
        }

        /* map iframe */
        .lb-map-wrap {
            flex: 1; min-height: 220px; position: relative;
            border-bottom: 1px solid var(--border);
        }
        .lb-map-wrap iframe {
            width: 100%; height: 100%; min-height: 220px;
            border: none; filter: grayscale(.55) invert(.88) hue-rotate(180deg) brightness(.9) contrast(1.1);
        }
        .lb-map-overlay {
            position: absolute; inset: 0; pointer-events: none;
            background: linear-gradient(to bottom, var(--smoke) 0%, transparent 18%, transparent 82%, var(--smoke) 100%);
        }

        /* footer info */
        .lb-panel-foot {
            padding: 1.5rem 2rem;
        }
        .lb-panel-category {
            font-size: .55rem; letter-spacing: .2em; text-transform: uppercase;
            color: var(--muted);
        }

        @media (max-width: 768px) {
            .lightbox.open { flex-direction: column; }
            .lb-right { width: 100%; flex-shrink: 0; height: 60vh; }
            .lb-left { padding: 1.5rem 1rem; }
            .lightbox-img { max-height: 40vh; }
        }

        /* ── LIGHTBOX — ícono descarga ── */
        .lb-buy-icon-wrap {
            padding: 1.2rem 2rem 1.6rem;
            border-top: 1px solid var(--border);
            display: flex; justify-content: center;
        }
        .lb-buy-icon-btn {
            background: none; border: 1px solid rgba(237,227,204,.12);
            color: rgba(237,227,204,.3); cursor: none;
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            transition: border-color .25s, color .25s, background .25s;
        }
        .lb-buy-icon-btn:hover {
            border-color: rgba(237,227,204,.5); color: var(--cream);
            background: rgba(237,227,204,.05);
        }

        /* ── PURCHASE MODAL ── */
        .purchase-modal {
            position: fixed; inset: 0; z-index: 3000;
            display: none; align-items: center; justify-content: center;
            background: rgba(8,7,4,.82);
            backdrop-filter: blur(10px);
            padding: 2rem;
        }
        .purchase-modal.open { display: flex; }
        .purchase-card {
            background: var(--smoke); border: 1px solid var(--border);
            padding: 2.8rem 2.5rem 2.2rem;
            max-width: 360px; width: 100%; position: relative;
            animation: lbIn .28s ease;
        }
        .pm-close {
            position: absolute; top: 1.1rem; right: 1.4rem;
            background: none; border: none; cursor: none;
            font-size: .5rem; letter-spacing: .2em; text-transform: uppercase;
            color: rgba(237,227,204,.25); transition: color .25s;
        }
        .pm-close:hover { color: var(--cream); }
        .pm-eyebrow {
            font-size: .46rem; letter-spacing: .3em; text-transform: uppercase;
            color: var(--terracotta); margin-bottom: .5rem;
        }
        .pm-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.35rem; color: var(--cream);
            line-height: 1.2; margin-bottom: 2rem;
        }
        .pm-price {
            font-family: 'DM Serif Display', serif;
            font-size: 3.2rem; font-weight: 300;
            color: var(--cream); line-height: 1;
        }
        .pm-price-cur {
            font-size: 1rem; opacity: .35;
            margin-left: .3rem; font-family: 'DM Sans', sans-serif;
        }
        .pm-sub {
            font-size: .46rem; letter-spacing: .2em; text-transform: uppercase;
            color: var(--muted); margin: .5rem 0 2rem;
        }
        .pm-pay-btns {
            display: flex; gap: .8rem;
        }
        .pm-pay-stripe, .pm-pay-paypal {
            flex: 1; display: flex; align-items: center; justify-content: center;
            padding: .9rem; text-decoration: none; cursor: none;
            transition: opacity .2s, transform .2s;
            border: 1px solid transparent;
        }
        .pm-pay-stripe { background: #635BFF; color: #fff; }
        .pm-pay-paypal { background: #0070BA; color: #fff; }
        .pm-pay-stripe:hover, .pm-pay-paypal:hover { opacity: .84; transform: translateY(-1px); }

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

        /* ── MAPA INTERACTIVO ── */
        .map-section { border-top: 1px solid var(--border); }
        .map-header {
            padding: 2.5rem 2.5rem 0;
            display: flex; align-items: center; justify-content: space-between;
        }
        #interactiveMap {
            width: 100%; height: 72vh; margin-top: 2rem;
        }
        /* Leaflet overrides */
        .leaflet-container { background: #e8e0d0; }
        .leaflet-control-zoom a {
            background: var(--smoke) !important;
            color: var(--cream) !important;
            border-color: var(--border) !important;
        }
        .leaflet-control-zoom a:hover { background: var(--border) !important; }
        .leaflet-container .leaflet-control-attribution {
            background: rgba(8,7,4,.75);
            color: rgba(237,227,204,.25);
            font-size: .42rem; letter-spacing: .08em;
        }
        .leaflet-container .leaflet-control-attribution a { color: rgba(237,227,204,.35); }
        /* Popup */
        .map-custom-popup .leaflet-popup-content-wrapper {
            background: var(--smoke);
            border: 1px solid var(--border);
            border-radius: 0;
            color: var(--cream);
            box-shadow: 0 12px 40px rgba(0,0,0,.75);
            padding: 0; overflow: hidden;
        }
        .map-custom-popup .leaflet-popup-tip-container { display: none; }
        .map-custom-popup .leaflet-popup-content { margin: 0; width: 220px !important; }
        .map-custom-popup .leaflet-popup-close-button {
            color: rgba(237,227,204,.3) !important;
            top: 6px !important; right: 8px !important;
            font-size: .85rem !important; z-index: 10;
        }
        .map-popup-img {
            width: 100%; height: 130px;
            background-size: cover; background-position: center;
        }
        .map-popup-body { padding: .85rem 1rem 1rem; }
        .map-popup-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1rem; line-height: 1.2;
            color: var(--cream); margin-bottom: .4rem;
        }
        .map-popup-alt {
            font-size: .5rem; letter-spacing: .18em; text-transform: uppercase;
            color: var(--ochre); margin-bottom: .2rem;
        }
        .map-popup-date {
            font-size: .5rem; letter-spacing: .15em; text-transform: uppercase;
            color: rgba(237,227,204,.32); margin-bottom: .2rem;
        }
        .map-popup-coords {
            font-size: .46rem; letter-spacing: .1em;
            color: rgba(237,227,204,.18); margin-bottom: .8rem;
        }
        .map-popup-link {
            display: inline-flex; align-items: center; gap: .4rem;
            font-size: .5rem; letter-spacing: .2em; text-transform: uppercase;
            color: var(--terracotta); background: none; border: none;
            cursor: pointer; padding: 0; transition: color .25s;
        }
        .map-popup-link:hover { color: var(--cream); }

        /* ── CONTACT (rediseño) ── */
        .contact-handle {
            font-family: 'DM Serif Display', serif;
            font-size: 1.05rem;
            font-weight: 400; line-height: 1; letter-spacing: .02em;
            color: rgba(237,227,204,.4); margin: .4rem 0 0;
        }
        .contact-handle .at { color: var(--terracotta); font-style: italic; }
        .contact-specialties {
            display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
            font-size: .55rem; letter-spacing: .24em; text-transform: uppercase;
            color: var(--muted); margin-bottom: 3.5rem;
        }
        .contact-specialties .sep { color: var(--border); }
        .contact-ig-btn {
            display: inline-flex; align-items: center; gap: .85rem;
            padding: .85rem 1.8rem;
            font-size: .55rem; letter-spacing: .22em; text-transform: uppercase;
            color: var(--cream); text-decoration: none;
            border: 1px solid rgba(237,227,204,.12);
            transition: border-color .3s, background .3s;
        }
        .contact-ig-btn:hover {
            border-color: var(--terracotta);
            background: rgba(196,87,31,.07);
        }
        .contact-ig-btn svg { transition: transform .3s; }
        .contact-ig-btn:hover svg { transform: translateX(5px); }
        .contact-geo {
            font-size: .52rem; letter-spacing: .2em; text-transform: uppercase;
            color: rgba(237,227,204,.18);
        }
        .contact-reasons {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 1px; background: var(--border);
            margin: 2rem 0 3.5rem;
        }
        .contact-card {
            background: var(--bg); padding: 2rem 1.8rem;
            transition: background .35s;
        }
        .contact-card:hover { background: var(--smoke); }
        .contact-card:hover .cc-num { color: var(--terracotta); }
        .cc-num {
            font-size: .5rem; letter-spacing: .28em; text-transform: uppercase;
            color: var(--border); margin-bottom: 1.2rem;
            transition: color .3s;
        }
        .cc-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.25rem; color: var(--cream);
            margin-bottom: .65rem; line-height: 1.15;
        }
        .cc-desc {
            font-size: .65rem; line-height: 1.8; color: var(--muted);
        }
        .contact-social-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            align-items: center; gap: 4rem;
            padding: 3rem 0 2rem;
        }
        .contact-social-title {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(2rem, 3.5vw, 3.2rem);
            font-weight: 300; line-height: 1.25;
            color: var(--cream); margin: 1rem 0 1rem;
        }
        .contact-social-desc {
            font-size: .78rem; line-height: 1.85;
            color: var(--muted); max-width: 38ch; margin-bottom: 1.8rem;
        }
        .contact-social-icons {
            display: flex; gap: 3.5rem; align-items: center;
            justify-content: center;
        }
        .social-icon-link {
            display: flex; flex-direction: column; align-items: center; gap: .8rem;
            color: rgba(237,227,204,.22); text-decoration: none;
            transition: color .35s, transform .35s;
        }
        .social-icon-link:hover { color: var(--cream); transform: translateY(-5px); }
        .social-icon-link span {
            font-size: .46rem; letter-spacing: .24em; text-transform: uppercase;
        }
        /* WhatsApp flotante */
        .whatsapp-float {
            position: fixed; bottom: 2rem; right: 2rem; z-index: 500;
            width: 56px; height: 56px; border-radius: 50%;
            background: #25D366;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 24px rgba(37,211,102,.4);
            text-decoration: none; cursor: none;
            transition: transform .3s, box-shadow .3s;
        }
        .whatsapp-float:hover {
            transform: scale(1.12);
            box-shadow: 0 6px 32px rgba(37,211,102,.6);
        }
        @media (max-width: 640px) {
            .contact-social-grid { grid-template-columns: 1fr; gap: 2.5rem; }
            .contact-social-icons { justify-content: flex-start; }
        }
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
            <a href="#mapa">Mapa</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero" id="hero">
        <img class="hero-img"
             src="/img/IMG_5098.JPEG"
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
                <button class="filter-btn" data-filter="alta-montana">Alta Montaña</button>
                <button class="filter-btn" data-filter="trekking">Trekking</button>
                <button class="filter-btn" data-filter="amazonia">Amazonía</button>
                <button class="filter-btn" data-filter="altiplano">Altiplano</button>
                <button class="filter-btn" data-filter="cultura">Cultura</button>
                <button class="filter-btn" data-filter="dron">Dron</button>
            </div>
        </div>

        <div class="gallery-grid" id="galleryGrid">

            <div class="gallery-item col-12 h-hero" data-cat="alta-montana" data-src="/img/IMG_0827.JPEG" data-title="Bolivia, 2025" data-category="Alta Montaña" data-location="Sajama" data-coords="18°06′S 68°53′O" data-alt="6.542 m" data-lat="-18.1" data-lng="-68.88" data-date="Febrero 2025">
                <img src="/img/IMG_0827.JPEG" alt="Bolivia, 2025">
                <div class="item-overlay"><div class="item-info"><p class="item-cat oc">Alta Montaña</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-7 h-tall" data-cat="trekking" data-src="/img/IMG_0963.JPEG" data-title="Bolivia, 2025" data-category="Trekking" data-location="Coroico" data-coords="16°11′S 67°44′O" data-alt="1.700 m" data-lat="-16.18" data-lng="-67.73" data-date="Marzo 2025">
                <img src="/img/IMG_0963.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Trekking</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-5 h-tall" data-cat="altiplano" data-src="/img/IMG_0980.JPEG" data-title="Bolivia, 2025" data-category="Altiplano" data-location="Oruro" data-coords="17°58′S 67°07′O" data-alt="3.706 m" data-lat="-17.97" data-lng="-67.12" data-date="Marzo 2025">
                <img src="/img/IMG_0980.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Altiplano</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-7 h-mid" data-cat="trekking" data-src="/img/IMG_1075.JPEG" data-title="Bolivia, 2025" data-category="Trekking" data-location="Sorata" data-coords="15°47′S 68°39′O" data-alt="2.695 m" data-lat="-15.78" data-lng="-68.65" data-date="Abril 2025">
                <img src="/img/IMG_1075.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Trekking</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-5 h-mid" data-cat="alta-montana" data-src="/img/IMG_1187.JPEG" data-title="Bolivia, 2025" data-category="Alta Montaña" data-location="Condoriri" data-coords="16°08′S 68°21′O" data-alt="5.648 m" data-lat="-16.13" data-lng="-68.35" data-date="Abril 2025">
                <img src="/img/IMG_1187.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat oc">Alta Montaña</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-4 h-tall" data-cat="amazonia" data-src="/img/IMG_1269.JPEG" data-title="Bolivia, 2025" data-category="Amazonía" data-location="Rurrenabaque" data-coords="14°26′S 67°32′O" data-alt="280 m" data-lat="-14.44" data-lng="-67.53" data-date="Mayo 2025">
                <img src="/img/IMG_1269.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat sg">Amazonía</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-4 h-tall" data-cat="amazonia" data-src="/img/IMG_1362.JPG" data-title="Bolivia, 2025" data-category="Amazonía" data-location="Trinidad" data-coords="14°50′S 64°54′O" data-alt="160 m" data-lat="-14.83" data-lng="-64.9" data-date="Mayo 2025">
                <img src="/img/IMG_1362.JPG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat sg">Amazonía</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-4 h-tall" data-cat="alta-montana" data-src="/img/IMG_1373.JPEG" data-title="Bolivia, 2025" data-category="Alta Montaña" data-location="Chearoco" data-coords="15°58′S 68°23′O" data-alt="6.127 m" data-lat="-15.96" data-lng="-68.38" data-date="Junio 2025">
                <img src="/img/IMG_1373.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat oc">Alta Montaña</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-8 h-mid" data-cat="altiplano" data-src="/img/IMG_1582.JPEG" data-title="Bolivia, 2025" data-category="Altiplano" data-location="Lago Titicaca" data-coords="15°51′S 69°20′O" data-alt="3.812 m" data-lat="-15.85" data-lng="-69.34" data-date="Junio 2025">
                <img src="/img/IMG_1582.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Altiplano</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-4 h-mid" data-cat="alta-montana" data-src="/img/IMG_1585.JPEG" data-title="Bolivia, 2025" data-category="Alta Montaña" data-location="Illimani" data-coords="16°38′S 67°47′O" data-alt="6.438 m" data-lat="-16.63" data-lng="-67.78" data-date="Julio 2025">
                <img src="/img/IMG_1585.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat oc">Alta Montaña</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-12 h-hero" data-cat="altiplano" data-src="/img/IMG_2101.JPG" data-title="Bolivia, 2025" data-category="Altiplano" data-location="Salar de Uyuni" data-coords="20°28′S 66°50′O" data-alt="3.656 m" data-lat="-20.46" data-lng="-66.83" data-date="Julio 2025">
                <img src="/img/IMG_2101.JPG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Altiplano</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-6 h-mid" data-cat="altiplano" data-src="/img/IMG_2225.JPG" data-title="Bolivia, 2025" data-category="Altiplano" data-location="Salar de Coipasa" data-coords="19°28′S 68°08′O" data-alt="3.657 m" data-lat="-19.47" data-lng="-68.13" data-date="Julio 2025">
                <img src="/img/IMG_2225.JPG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Altiplano</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-6 h-mid" data-cat="amazonia" data-src="/img/IMG_4130.JPG" data-title="Bolivia, 2025" data-category="Amazonía" data-location="Cobija, Pando" data-coords="11°02′S 68°46′O" data-alt="250 m" data-lat="-11.03" data-lng="-68.77" data-date="Agosto 2025">
                <img src="/img/IMG_4130.JPG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat sg">Amazonía</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-4 h-tall" data-cat="amazonia" data-src="/img/IMG_4131.JPG" data-title="Bolivia, 2025" data-category="Amazonía" data-location="Río Madre de Dios" data-coords="11°12′S 68°30′O" data-alt="220 m" data-lat="-11.2" data-lng="-68.5" data-date="Agosto 2025">
                <img src="/img/IMG_4131.JPG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat sg">Amazonía</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-4 h-tall" data-cat="alta-montana" data-src="/img/IMG_4321.JPG" data-title="Bolivia, 2025" data-category="Alta Montaña" data-location="Huayna Potosí" data-coords="16°17′S 68°09′O" data-alt="6.088 m" data-lat="-16.28" data-lng="-68.15" data-date="Agosto 2025">
                <img src="/img/IMG_4321.JPG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat oc">Alta Montaña</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-4 h-tall" data-cat="trekking" data-src="/img/IMG_4328.JPEG" data-title="Bolivia, 2025" data-category="Trekking" data-location="Yungas" data-coords="16°18′S 67°42′O" data-alt="2.200 m" data-lat="-16.3" data-lng="-67.7" data-date="Septiembre 2025">
                <img src="/img/IMG_4328.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Trekking</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-7 h-mid" data-cat="alta-montana" data-src="/img/IMG_4356.JPEG" data-title="Bolivia, 2025" data-category="Alta Montaña" data-location="Chachacomani" data-coords="15°47′S 68°43′O" data-alt="6.074 m" data-lat="-15.78" data-lng="-68.72" data-date="Septiembre 2025">
                <img src="/img/IMG_4356.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat oc">Alta Montaña</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-5 h-mid" data-cat="dron" data-src="/img/IMG_4366.JPEG" data-title="Bolivia, 2025" data-category="Dron · Aéreo" data-location="La Paz" data-coords="16°30′S 68°09′O" data-alt="3.625 m" data-lat="-16.5" data-lng="-68.15" data-date="Septiembre 2025">
                <img src="/img/IMG_4366.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat oc">Dron</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-12 h-hero" data-cat="dron" data-src="/img/IMG_5098.JPEG" data-title="Bolivia, 2025" data-category="Dron · Aéreo" data-location="Altiplano Central" data-coords="17°30′S 67°48′O" data-alt="3.800 m" data-lat="-17.5" data-lng="-67.8" data-date="Septiembre 2025">
                <img src="/img/IMG_5098.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat oc">Dron</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-4 h-mid" data-cat="amazonia" data-src="/img/IMG_5114.JPEG" data-title="Bolivia, 2025" data-category="Amazonía" data-location="Beni" data-coords="14°12′S 65°48′O" data-alt="200 m" data-lat="-14.2" data-lng="-65.8" data-date="Octubre 2025">
                <img src="/img/IMG_5114.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat sg">Amazonía</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-4 h-mid" data-cat="altiplano" data-src="/img/IMG_5120.JPEG" data-title="Bolivia, 2025" data-category="Altiplano" data-location="Potosí" data-coords="19°35′S 65°45′O" data-alt="3.976 m" data-lat="-19.58" data-lng="-65.75" data-date="Octubre 2025">
                <img src="/img/IMG_5120.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Altiplano</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-4 h-mid" data-cat="alta-montana" data-src="/img/IMG_5144.JPEG" data-title="Bolivia, 2025" data-category="Alta Montaña" data-location="Chiar Khota" data-coords="16°27′S 68°07′O" data-alt="5.350 m" data-lat="-16.45" data-lng="-68.12" data-date="Octubre 2025">
                <img src="/img/IMG_5144.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat oc">Alta Montaña</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-8 h-tall" data-cat="trekking" data-src="/img/IMG_5297.JPG" data-title="Bolivia, 2025" data-category="Trekking" data-location="Valle de Zongo" data-coords="16°09′S 68°07′O" data-alt="2.800 m" data-lat="-16.15" data-lng="-68.12" data-date="Octubre 2025">
                <img src="/img/IMG_5297.JPG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Trekking</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-4 h-tall" data-cat="trekking" data-src="/img/IMG_5645.JPEG" data-title="Bolivia, 2025" data-category="Trekking" data-location="Valle de la Luna" data-coords="16°34′S 68°06′O" data-alt="3.400 m" data-lat="-16.57" data-lng="-68.1" data-date="Noviembre 2025">
                <img src="/img/IMG_5645.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Trekking</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-6 h-mid" data-cat="amazonia" data-src="/img/IMG_5806.JPG" data-title="Bolivia, 2025" data-category="Amazonía" data-location="Madidi" data-coords="13°30′S 68°00′O" data-alt="300 m" data-lat="-13.5" data-lng="-68.0" data-date="Noviembre 2025">
                <img src="/img/IMG_5806.JPG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat sg">Amazonía</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-6 h-mid" data-cat="altiplano" data-src="/img/IMG_6037.JPEG" data-title="Bolivia, 2025" data-category="Altiplano" data-location="PN Sajama" data-coords="18°18′S 68°57′O" data-alt="4.200 m" data-lat="-18.3" data-lng="-68.95" data-date="Noviembre 2025">
                <img src="/img/IMG_6037.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Altiplano</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>

            <div class="gallery-item col-4 h-mid" data-cat="amazonia" data-src="/img/IMG_6047.JPEG" data-title="Bolivia, 2025" data-category="Amazonía" data-location="Noël Kempff" data-coords="14°42′S 60°48′O" data-alt="180 m" data-lat="-14.7" data-lng="-60.8" data-date="Noviembre 2025">
                <img src="/img/IMG_6047.JPEG" alt="Bolivia, 2025" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat sg">Amazonía</p><p class="item-title">Bolivia, 2025</p></div></div>
            </div>
            <div class="gallery-item col-4 h-mid" data-cat="altiplano" data-src="/img/IMG_6079.JPEG" data-title="Laguna Colorada" data-category="Altiplano" data-location="Laguna Colorada, Potosí" data-coords="-22.17, -67.78" data-lat="-22.17" data-lng="-67.78" data-alt="4278 msnm" data-date="Noviembre 2025">
                <img src="/img/IMG_6079.JPEG" alt="Laguna Colorada" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Altiplano</p><p class="item-title">Laguna Colorada</p></div></div>
            </div>
            <div class="gallery-item col-4 h-mid" data-cat="dron" data-src="/img/IMG_7278.JPG" data-title="Salar de Uyuni" data-category="Dron · Aéreo" data-location="Salar de Uyuni, Potosí" data-coords="-20.30, -67.00" data-lat="-20.30" data-lng="-67.00" data-alt="3660 msnm" data-date="Diciembre 2025">
                <img src="/img/IMG_7278.JPG" alt="Salar de Uyuni" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat oc">Dron</p><p class="item-title">Salar de Uyuni</p></div></div>
            </div>

            <div class="gallery-item col-7 h-tall" data-cat="alta-montana" data-src="/img/IMG_7727.JPEG" data-title="Nevado Sajama" data-category="Alta Montaña" data-location="Nevado Sajama, Oruro" data-coords="-18.10, -68.88" data-lat="-18.10" data-lng="-68.88" data-alt="6542 msnm" data-date="Diciembre 2025">
                <img src="/img/IMG_7727.JPEG" alt="Nevado Sajama" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat oc">Alta Montaña</p><p class="item-title">Nevado Sajama</p></div></div>
            </div>
            <div class="gallery-item col-5 h-tall" data-cat="cultura" data-src="/img/IMG_8382.JPG" data-title="La Paz" data-category="Ciudades · Cultura" data-location="La Paz, Bolivia" data-coords="-16.50, -68.15" data-lat="-16.50" data-lng="-68.15" data-alt="3625 msnm" data-date="Diciembre 2025">
                <img src="/img/IMG_8382.JPG" alt="La Paz" loading="lazy">
                <div class="item-overlay"><div class="item-info"><p class="item-cat tc">Cultura</p><p class="item-title">La Paz</p></div></div>
            </div>

        </div>
    </section>

    <!-- MAPA INTERACTIVO -->
    <section class="map-section" id="mapa">
        <div class="map-header">
            <div class="section-label">
                <span class="section-number">02</span> Mapa
            </div>
            <span class="locations-count">10 ubicaciones · 2025</span>
        </div>
        <div id="interactiveMap"></div>
    </section>

    <!-- ABOUT -->
    <section class="about-section" id="about">
        <div class="about-photo">
            <img src="/img/profile/foto_mark.jpeg" alt="Marco">
        </div>
        <div class="about-text">
            <div class="section-label" style="max-width:280px">
                <span class="section-number">03</span> About
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
            <span class="section-number">04</span> Contact
        </div>

        <div class="contact-social-grid">

            <!-- Izquierda: texto -->
            <div>
                <h3 class="contact-social-title">
                    Seguí el trabajo<br>en tiempo real.
                </h3>
                <p class="contact-social-desc">
                    Expediciones en curso, detrás de cámara y el archivo completo.
                    El mejor lugar para colaborar o hacer una consulta es por mensaje directo.
                </p>
                <p class="contact-handle"><span class="at">@</span>markualko</p>
            </div>

            <!-- Derecha: iconos -->
            <div class="contact-social-icons">

                <a href="https://www.instagram.com/markualko"
                   target="_blank" rel="noopener"
                   class="social-icon-link" aria-label="Instagram">
                    <svg width="68" height="68" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width=".9" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                        <circle cx="12" cy="12" r="4.5"/>
                        <circle cx="17.5" cy="6.5" r=".8" fill="currentColor" stroke="none"/>
                    </svg>
                    <span>Instagram</span>
                </a>

                <a href="https://www.threads.net/@markualko"
                   target="_blank" rel="noopener"
                   class="social-icon-link" aria-label="Threads">
                    <svg width="60" height="60" viewBox="0 0 192 192" fill="currentColor">
                        <path d="M141.537 88.988c-.827-.396-1.667-.777-2.518-1.142-1.482-27.307-16.403-42.94-41.457-43.1h-.364c-14.986 0-27.449 6.396-35.12 17.036l13.779 9.452c5.73-8.695 14.724-10.548 21.347-10.548.077 0 .158 0 .234.001 8.249.053 14.474 2.451 18.503 7.129 2.932 3.405 4.893 8.111 5.864 14.05-7.314-1.243-15.224-1.625-23.68-1.14C74.325 83.095 59.011 96.988 60.04 116.292c.522 9.792 5.4 18.216 13.735 23.719 7.047 4.652 16.124 6.927 25.557 6.412 12.458-.683 22.231-5.436 29.049-14.127 5.178-6.6 8.453-15.153 9.899-25.93 5.937 3.583 10.337 8.298 12.767 13.966 4.132 9.635 4.373 25.468-8.546 38.376-11.319 11.308-24.925 16.2-45.487 16.351-22.809-.169-40.06-7.484-51.276-21.742C35.236 139.966 29.808 120.682 29.605 96c.203-24.682 5.631-43.966 16.134-57.317C56.954 24.425 74.204 17.11 97.013 16.94c22.975.171 40.526 7.521 52.171 21.848 5.71 7.026 10.015 15.861 12.853 26.162l16.147-4.308c-3.44-12.68-8.853-23.607-16.219-32.668C147.036 9.607 125.202.195 97.07 0h-.113C68.882.194 47.292 9.642 32.788 28.079 19.882 44.486 13.224 67.316 13.001 95.933L13 96l.001.068c.223 28.616 6.88 51.446 19.787 67.853 14.504 18.437 36.094 27.885 64.169 28.079h.113c25.06-.173 42.654-6.708 57.148-21.189 18.963-18.945 18.392-42.692 12.142-57.27-4.484-10.454-13.033-19.945-24.723-25.553zm-57.457 40.52c-10.44.587-21.286-4.099-21.821-14.136-.363-7.442 5.329-15.747 22.495-16.736a100.88 100.88 0 016.727-.232c4.17 0 8.093.37 11.747 1.083-1.978 24.342-13.385 28.713-19.148 29.021z"/>
                    </svg>
                    <span>Threads</span>
                </a>

            </div>
        </div>

        <div class="contact-row" style="border-top: 1px solid var(--border); padding-top: 2rem;">
            <span class="contact-geo">Bolivia · Argentina · Chile</span>
        </div>
    </section>

    <!-- PURCHASE MODAL -->
    <div class="purchase-modal" id="purchaseModal">
        <div class="purchase-card">
            <button class="pm-close" id="pmClose">Cerrar ✕</button>
            <p class="pm-eyebrow">Descargar imagen</p>
            <p class="pm-title" id="pmTitle"></p>
            <p class="pm-price">$25<span class="pm-price-cur">USD</span></p>
            <p class="pm-sub">Alta resolución · Licencia incluida</p>
            <div class="pm-pay-btns">
                <!-- Stripe -->
                <a href="#" class="pm-pay-stripe" id="pmStripe" aria-label="Pagar con Stripe">
                    <img src="https://cdn.simpleicons.org/stripe/ffffff" height="22" alt="Stripe">
                </a>
                <!-- PayPal -->
                <a href="#" class="pm-pay-paypal" id="pmPaypal" aria-label="Pagar con PayPal">
                    <img src="https://cdn.simpleicons.org/paypal/ffffff" height="22" alt="PayPal">
                </a>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <span>© {{ date('Y') }} MarKual — @markualko</span>
        <span>Dron · Alta Montaña · Amazonía</span>
    </footer>

    <!-- LIGHTBOX -->
    <div class="lightbox" id="lightbox">
        <button class="lb-close" id="lbClose">Cerrar ✕</button>

        <!-- izquierda: foto -->
        <div class="lb-left">
            <img class="lightbox-img" id="lbImg" src="" alt="">
            <div class="lb-nav">
                <button class="lb-prev" id="lbPrev">&#8592;</button>
                <button class="lb-next" id="lbNext">&#8594;</button>
            </div>
        </div>

        <!-- derecha: info + mapa -->
        <div class="lb-right">
            <div class="lb-panel-head">
                <div class="lb-panel-eyebrow">
                    <span class="gps-dot"></span>
                    Ubicación
                </div>
                <div class="lb-panel-name" id="lbLocName"><span class="lb-cursor"></span></div>
                <div class="lb-panel-meta">
                    <span id="lbLocCoords"></span>
                    <span class="meta-alt" id="lbLocAlt"></span>
                </div>
            </div>

            <div class="lb-route-wrap">
                <p class="lb-route-label">Ruta trazada</p>
                <svg class="lb-route-svg" id="lbRouteSvg" viewBox="0 0 320 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Líneas de terreno decorativas -->
                    <path class="lb-route-terrain" d="M 0,65 Q 40,55 80,60 T 160,55 T 240,58 T 320,52"/>
                    <path class="lb-route-terrain" d="M 0,72 Q 50,65 100,68 T 200,64 T 320,60"/>
                    <!-- Ruta principal -->
                    <path class="lb-route-path" id="lbRoutePath"
                          d="M 20,62 C 50,58 70,30 110,28 S 180,42 220,35 S 280,20 300,22"/>
                    <!-- Punto de origen -->
                    <circle cx="20" cy="62" r="3" fill="rgba(196,87,31,.4)"/>
                    <!-- Destino -->
                    <circle class="lb-route-dot" id="lbRouteDot" cx="300" cy="22" r="5"/>
                    <circle class="lb-route-dot-pulse" id="lbRoutePulse" cx="300" cy="22" r="5"/>
                </svg>
            </div>

            <div class="lb-map-wrap">
                <iframe id="lbMapFrame" src="" loading="lazy" title="Mapa"></iframe>
                <div class="lb-map-overlay"></div>
            </div>

            <div class="lb-panel-foot">
                <p class="lb-panel-category" id="lbCaption"></p>
            </div>

            <div class="lb-buy-icon-wrap">
                <button class="lb-buy-icon-btn" id="lbDownloadBtn" title="Descargar imagen">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3v13M7 11l5 5 5-5M3 21h18"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- VIDEO MODAL -->
    <div class="video-modal" id="videoModal">
        <div class="video-wrap">
            <button class="vm-close" id="vmClose">Cerrar ✕</button>
            <iframe id="videoFrame" src="" allowfullscreen></iframe>
        </div>
    </div>

    <!-- WHATSAPP FLOTANTE — reemplazá XXXXXXXXXXX con el número completo sin + ni espacios -->
    <a href="https://wa.me/XXXXXXXXXXX" target="_blank" rel="noopener"
       class="whatsapp-float" aria-label="WhatsApp">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
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
    const photoItems  = ()=> [...document.querySelectorAll('.gallery-item[data-src]:not(.hidden-item)')];
    let lbIdx = 0;
    const lightbox    = document.getElementById('lightbox');
    const lbImg       = document.getElementById('lbImg');
    const lbCap       = document.getElementById('lbCaption');
    const lbLocName   = document.getElementById('lbLocName');
    const lbLocCoords = document.getElementById('lbLocCoords');
    const lbLocAlt    = document.getElementById('lbLocAlt');
    const lbMapFrame  = document.getElementById('lbMapFrame');
    const lbRoutePath = document.getElementById('lbRoutePath');
    const lbRouteDot  = document.getElementById('lbRouteDot');
    const lbRoutePulse= document.getElementById('lbRoutePulse');
    let typeTimer = null;

    function typeText(el, text, speed=40){
        clearTimeout(typeTimer);
        // conserva el cursor span
        el.innerHTML = '<span class="lb-cursor"></span>';
        let i = 0;
        function next(){
            el.innerHTML = text.slice(0,i) + '<span class="lb-cursor"></span>';
            if(i++ < text.length) typeTimer = setTimeout(next, speed);
        }
        next();
    }

    function restartRouteAnim(){
        [lbRoutePath, lbRouteDot, lbRoutePulse].forEach(el=>{
            el.classList.remove('animate');
            void el.getBoundingClientRect(); // fuerza reflow
            el.classList.add('animate');
        });
    }

    function showLb(idx){
        const items = photoItems(); if(!items.length) return;
        lbIdx = (idx + items.length) % items.length;
        const el = items[lbIdx];

        // foto
        lbImg.src = el.dataset.src;
        lbImg.style.animation = 'none';
        void lbImg.getBoundingClientRect();
        lbImg.style.animation = '';

        // caption
        lbCap.textContent = el.dataset.category + ' — ' + el.dataset.title;

        // nombre con tipeo
        const loc = el.dataset.location || '';
        lbLocCoords.textContent = el.dataset.coords || '';
        lbLocAlt.textContent    = el.dataset.alt    || '';
        setTimeout(()=> typeText(lbLocName, loc, 42), 200);

        // mapa
        const lat = el.dataset.lat, lng = el.dataset.lng;
        if(lat && lng){
            const zoom = Math.abs(parseFloat(el.dataset.alt)) > 3000 ? 9 : 12;
            lbMapFrame.src = `https://maps.google.com/maps?q=${lat},${lng}&z=${zoom}&output=embed`;
        } else {
            lbMapFrame.src = '';
        }

        // animación ruta
        restartRouteAnim();

        // botón de compra
        updateBuyBtn();
    }

    function closeLb(){
        lightbox.classList.remove('open');
        document.body.style.overflow = '';
        clearTimeout(typeTimer);
        lbMapFrame.src = '';
    }

    document.querySelectorAll('.gallery-item[data-src]').forEach(el=>{
        el.addEventListener('click', ()=>{
            lbIdx = photoItems().indexOf(el);
            showLb(lbIdx);
            lightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
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

    // ── PURCHASE MODAL ───────────────────────
    const purchaseModal = document.getElementById('purchaseModal');
    const pmTitle       = document.getElementById('pmTitle');

    function openPurchaseModal(){
        const items = photoItems();
        if(!items.length) return;
        pmTitle.textContent = items[lbIdx].dataset.title || '';
        purchaseModal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closePurchaseModal(){
        purchaseModal.classList.remove('open');
    }

    document.getElementById('lbDownloadBtn').addEventListener('click', openPurchaseModal);
    document.getElementById('pmClose').addEventListener('click', closePurchaseModal);
    purchaseModal.addEventListener('click', e => { if(e.target === purchaseModal) closePurchaseModal(); });

    function updateBuyBtn(){} // reset al cambiar foto (modal se cierra solo con Escape)

    // ── KEYBOARD ─────────────────────────────
    document.addEventListener('keydown', e=>{
        if(e.key==='Escape')     { closeLb(); closeVm(); closePurchaseModal(); }
        if(e.key==='ArrowRight') showLb(lbIdx+1);
        if(e.key==='ArrowLeft')  showLb(lbIdx-1);
    });

    // ── MAPA INTERACTIVO ─────────────────────
    (function initMap(){
        const mapEl = document.getElementById('interactiveMap');
        if(!mapEl || typeof L === 'undefined') return;

        const map = L.map('interactiveMap', {
            center: [-19, -66.5],
            zoom: 5,
            scrollWheelZoom: false,
            zoomControl: true,
        });
        setTimeout(() => map.invalidateSize(), 300);

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
            maxZoom: 18
        }).addTo(map);

        const catColor = {
            'alta-montana': '#d4903a',
            trekking:       '#c4571f',
            amazonia:       '#7a9468',
            altiplano:      '#c4571f',
            cultura:        '#7a6550',
            dron:           '#d4903a'
        };

        function makeIcon(color){
            return L.divIcon({
                className: '',
                html: `<svg width="22" height="30" viewBox="0 0 22 30" xmlns="http://www.w3.org/2000/svg">
                         <path d="M11 0C4.925 0 0 4.925 0 11c0 8.25 11 19 11 19S22 19.25 22 11C22 4.925 17.075 0 11 0z" fill="${color}"/>
                         <circle cx="11" cy="11" r="4" fill="rgba(8,7,4,.6)"/>
                       </svg>`,
                iconSize:    [22, 30],
                iconAnchor:  [11, 30],
                popupAnchor: [0, -32]
            });
        }

        // permite abrir el lightbox desde el popup del mapa
        window.openPhotoFromMap = function(src){
            const item = [...document.querySelectorAll('.gallery-item[data-src]')]
                .find(el => el.dataset.src === src);
            if(!item) return;
            lbIdx = photoItems().indexOf(item);
            showLb(lbIdx);
            lightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
        };

        document.querySelectorAll('.gallery-item[data-lat]').forEach(el => {
            const lat   = parseFloat(el.dataset.lat);
            const lng   = parseFloat(el.dataset.lng);
            const color = catColor[el.dataset.cat] || '#c4571f';
            const marker = L.marker([lat, lng], { icon: makeIcon(color) }).addTo(map);

            const src    = el.dataset.src    || '';
            const title  = el.dataset.title  || '';
            const alt    = el.dataset.alt    || '';
            const date   = el.dataset.date   || '';
            const coords = el.dataset.coords || '';

            const popup = `
                <div class="map-popup">
                    <div class="map-popup-img" style="background-image:url('${src}')"></div>
                    <div class="map-popup-body">
                        <p class="map-popup-title">${title}</p>
                        ${alt    ? `<p class="map-popup-alt">${alt}</p>`       : ''}
                        ${date   ? `<p class="map-popup-date">${date}</p>`     : ''}
                        ${coords ? `<p class="map-popup-coords">${coords}</p>` : ''}
                        <button class="map-popup-link"
                                onclick="openPhotoFromMap('${src}')">Ver foto →</button>
                    </div>
                </div>`;

            marker.bindPopup(popup, { maxWidth: 230, className: 'map-custom-popup' });
        });
    })();
    </script>

</body>
</html>
