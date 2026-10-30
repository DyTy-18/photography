// Animaciones de scroll del storyboard.
//
// Reparto:
// - Lenis da el scroll suave; GSAP ScrollTrigger sincroniza cada efecto con el scroll (scrub).
// - Las secciones "ancladas" no usan pin de GSAP: son un contenedor alto con un hijo
//   position: sticky. Se lleva mejor con Lenis y con el teclado, y sin JS sigue funcionando.
// - Todo vive dentro de matchMedia('(prefers-reduced-motion: no-preference)'). Con
//   reduced-motion no se crea nada y la página queda estática y completa (ver app.css: .motion-ok).

import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
import { initFog } from './fog';

gsap.registerPlugin(ScrollTrigger);

const GLIDE = 'expo.out'; // ≈ cubic-bezier(0.19, 1, 0.22, 1)

export function initStory() {
    const mm = gsap.matchMedia();

    mm.add('(prefers-reduced-motion: no-preference)', () => {
        document.documentElement.classList.add('motion-ok');

        // ── Scroll suave ─────────────────────────────
        const lenis = new Lenis({ lerp: 0.09, anchors: true });
        lenis.on('scroll', ScrollTrigger.update);
        const raf = (time) => lenis.raf(time * 1000);
        gsap.ticker.add(raf);
        gsap.ticker.lagSmoothing(0);
        window.lenis = lenis; // el lightbox lo para y lo reanuda

        window.scrollTo(0, 0);
        intro();
        cards();
        zooms();
        tiles();
        layers();
        drift();
        stack();
        lines();
        reveals();

        ScrollTrigger.refresh();
        // El archivo cambia de alto al filtrar o al cargar más fotos
        const onArchive = () => ScrollTrigger.refresh();
        window.addEventListener('archive:resize', onArchive);
        // Las fotos lazy cambian alturas al cargar: un refresco cuando termina la carga
        window.addEventListener('load', onArchive, { once: true });

        return () => {
            window.removeEventListener('archive:resize', onArchive);
            gsap.ticker.remove(raf);
            lenis.destroy();
            window.lenis = null;
            document.documentElement.classList.remove('motion-ok');
        };
    });
}

// 01 · Intro: la neblina se despeja con el scroll y el titular sale de ella.
// Con reduced-motion no se llega aquí: se ve la foto tal cual, sin canvas.
function intro() {
    const section = document.querySelector('[data-intro]');
    if (!section) return;
    const canvas = section.querySelector('[data-intro-fog]');
    const img = section.querySelector('[data-intro-photo] img');
    const lines = section.querySelectorAll('[data-intro-line]');
    const fog = canvas && img ? initFog(canvas, img) : null;
    if (!fog) section.classList.add('no-fog'); // sin WebGL: foto nítida, sin canvas
    // Arranca desde el difuminado (foto borrosa, niebla suave), no desde la niebla cerrada
    const state = { fog: 0.6 };
    fog?.setFog(state.fog);

    // El titular empieza escondido en la niebla
    gsap.set(lines, { opacity: 0, filter: 'blur(14px)', yPercent: 30 });

    gsap.timeline({
        scrollTrigger: { trigger: section, start: 'top top', end: 'bottom bottom', scrub: 0.8 },
        defaults: { ease: 'none' },
    })
        .to(state, { fog: 0, duration: 0.75, onUpdate: () => fog?.setFog(state.fog) }, 0)
        .to(lines, { opacity: 1, filter: 'blur(0px)', yPercent: 0, stagger: 0.08, duration: 0.3, ease: 'power2.out' }, 0.5);

    // WhatsApp aparece cuando el titular ya salió de la niebla (y se esconde si se vuelve arriba)
    const wa = document.querySelector('[data-wa]');
    if (wa) {
        const toggle = (on) => { wa.classList.toggle('is-on', on); wa.inert = !on; };
        toggle(false);
        ScrollTrigger.create({
            trigger: section,
            start: () => `top+=${(section.offsetHeight - window.innerHeight) * 0.75} top`,
            end: 'max',
            invalidateOnRefresh: true,
            onToggle: (self) => toggle(self.isActive),
        });
    }
}

// Tarjetas con parallax interno: la foto se mueve más lenta que su marco
function cards() {
    gsap.utils.toArray('[data-parallax]').forEach((frame) => {
        const img = frame.querySelector('img');
        const amount = parseFloat(frame.dataset.parallax || '12');
        gsap.fromTo(img, { yPercent: -amount }, {
            yPercent: amount, ease: 'none',
            scrollTrigger: { trigger: frame, start: 'top bottom', end: 'bottom top', scrub: true },
        });
    });

    // Elementos que se desplazan a distinta velocidad (capas)
    gsap.utils.toArray('[data-speed]').forEach((el) => {
        const speed = parseFloat(el.dataset.speed);
        gsap.to(el, {
            yPercent: speed * -100, ease: 'none',
            scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true },
        });
    });
}

// Transición cenital: la foto se abre desde un marco hasta llenar la pantalla y sigue acercándose
function zooms() {
    gsap.utils.toArray('[data-zoom]').forEach((section) => {
        const frame = section.querySelector('[data-zoom-frame]');
        const img = frame.querySelector('img');
        const caption = section.querySelector('[data-zoom-caption]');
        gsap.timeline({
            scrollTrigger: { trigger: section, start: 'top top', end: 'bottom bottom', scrub: 0.6 },
            defaults: { ease: 'none' },
        })
            .fromTo(frame, { clipPath: 'inset(22% 28% 22% 28%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 0.45 }, 0)
            .fromTo(img, { scale: 1.25 }, { scale: 1, duration: 0.45 }, 0)
            // ya abierta, se queda y se acerca muy despacio; la siguiente sección entra por encima
            .to(img, { scale: 1.08, duration: 0.55 }, 0.45)
            .fromTo(caption, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.15 }, 0.3);
    });
}

// Rejilla rotada: las filas se deslizan en direcciones opuestas
function tiles() {
    gsap.utils.toArray('[data-tiles]').forEach((section) => {
        section.querySelectorAll('[data-tiles-row]').forEach((row, i) => {
            const dir = i % 2 ? 1 : -1;
            gsap.fromTo(row, { xPercent: dir * -8 }, {
                xPercent: dir * 8, ease: 'none',
                scrollTrigger: { trigger: section, start: 'top bottom', end: 'bottom top', scrub: true },
            });
        });
    });
}

// Bandas a sangre con el texto en otra capa
function layers() {
    gsap.utils.toArray('[data-band]').forEach((band) => {
        const img = band.querySelector('img');
        const title = band.querySelector('[data-band-title]');
        const st = { trigger: band, start: 'top bottom', end: 'bottom top', scrub: true };
        gsap.fromTo(img, { yPercent: -14 }, { yPercent: 14, ease: 'none', scrollTrigger: st });
        if (title) gsap.fromTo(title, { yPercent: 60 }, { yPercent: -60, ease: 'none', scrollTrigger: { ...st } });
    });
}

// Panorámica: la foto es más ancha que la pantalla y se desliza de lado mientras la banda pasa.
// No ancla la sección: el scroll sigue igual que en el resto de la página.
function drift() {
    gsap.utils.toArray('[data-drift]').forEach((section) => {
        const track = section.querySelector('[data-drift-track]');
        gsap.fromTo(track, { x: 0 }, {
            x: () => -(track.offsetWidth - section.clientWidth), ease: 'none',
            scrollTrigger: { trigger: section, start: 'top bottom', end: 'bottom top', scrub: true, invalidateOnRefresh: true },
        });
    });
}

// Pila de la cumbre: cada foto se queda fija y la siguiente la tapa; la de abajo se aleja
function stack() {
    const cards = gsap.utils.toArray('[data-stack-card]');
    cards.forEach((card, i) => {
        const next = cards[i + 1];
        if (!next) return;
        const inner = card.querySelector('[data-stack-inner]');
        gsap.to(inner, {
            scale: 0.9, opacity: 0.35, ease: 'none',
            scrollTrigger: { trigger: next, start: 'top bottom', end: 'top top', scrub: true },
        });
    });

    // Contador de altitud en las cumbres
    gsap.utils.toArray('[data-peak]').forEach((el) => {
        const target = parseInt(el.dataset.peak.replace(/\D/g, ''), 10);
        const obj = { v: Math.round(target * 0.8) };
        gsap.to(obj, {
            v: target, ease: 'none',
            scrollTrigger: { trigger: el.closest('[data-stack-card]'), start: 'top 80%', end: 'top 10%', scrub: true },
            // a mano: en español los números de 4 cifras no llevan separador con toLocaleString
            onUpdate: () => { el.textContent = String(Math.round(obj.v)).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' m'; },
        });
    });
}

// Manifiesto: cada línea sube desde su máscara al entrar
function lines() {
    gsap.utils.toArray('[data-lines]').forEach((block) => {
        gsap.fromTo(block.querySelectorAll('[data-line]'), { yPercent: 110 }, {
            yPercent: 0, duration: 0.8, ease: GLIDE, stagger: 0.08,
            scrollTrigger: { trigger: block, start: 'top 75%' },
        });
    });
}

// Aparición simple para bloques de texto y fotos sueltas
function reveals() {
    gsap.utils.toArray('[data-reveal]').forEach((el) => {
        gsap.fromTo(el, { opacity: 0, y: 32 }, {
            opacity: 1, y: 0, duration: 0.8, ease: GLIDE,
            delay: parseFloat(el.dataset.reveal || '0'),
            scrollTrigger: { trigger: el, start: 'top 88%' },
        });
    });
}
