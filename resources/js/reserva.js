// Popup de rutas (#rutaModal): carrusel de fotos de la ruta + formulario de reserva.
// El formulario guarda el lead y abre WhatsApp con el mensaje ya escrito (no hay correo).
// Se envía por fetch y los errores aparecen junto a cada campo.

export function initReserva() {
    const modal = document.getElementById('rutaModal');
    const form = document.getElementById('reservaForm');
    if (!modal || !form) return;

    const fotos = document.getElementById('rmFotos');
    const count = document.getElementById('rmCount');
    const prev = document.getElementById('rmPrev');
    const next = document.getElementById('rmNext');
    const panel = document.getElementById('rmPanel');
    const ok = document.getElementById('reservaOk');
    const wa = document.getElementById('reservaWa');
    const general = document.getElementById('reservaError');
    const submit = form.querySelector('[type="submit"]');
    let lastFocus = null;
    let slugActual = null;
    const base = modal.dataset.share; // https://.../rutas
    const syncUrl = 'syncUrl' in modal.dataset;

    // ── Abrir / cerrar ──
    function open(slug, trigger) {
        const tpl = document.querySelector(`template[data-ruta-tpl="${slug}"]`);
        if (!tpl) return;

        for (const key of ['titulo', 'zona', 'meta', 'resumen']) {
            document.getElementById('rm' + key[0].toUpperCase() + key.slice(1)).textContent = tpl.dataset[key];
        }
        fotos.replaceChildren(tpl.content.cloneNode(true));
        fotos.scrollLeft = 0;
        shown = 0;
        updateCarousel();

        // Formulario limpio cada vez, con la ruta del popup ya elegida
        form.hidden = false;
        ok.hidden = true;
        general.hidden = true;
        clearAll();
        form.elements.ruta.value = slug;

        lastFocus = trigger;
        slugActual = slug;
        resetShare();
        // En /rutas la barra de direcciones muestra el enlace de la ruta abierta
        if (syncUrl) history.replaceState(null, '', `${base}/${slug}${location.search}`);
        panel.scrollTop = 0;
        closing = false;
        modal.showModal();
        document.body.style.overflow = 'hidden';
        window.lenis?.stop();
        animateIn();
    }

    // ── Movimiento del popup (WAAPI; la salida del <dialog> necesita JS) ──
    // Entrada: fondo y ventana juntos (400ms), la foto se asienta y el texto llega escalonado.
    // Salida: más corta (200ms), por el mismo camino. Curva del proyecto: --ease-out.
    const EASE = getComputedStyle(document.documentElement).getPropertyValue('--ease-out').trim() || 'cubic-bezier(0.23, 1, 0.32, 1)';
    const reduced = () => matchMedia('(prefers-reduced-motion: reduce)').matches;
    const staggered = () => [...panel.querySelectorAll('#rmMeta, #rmTitulo, #rmZona, #rmResumen'), panel.lastElementChild];
    let closing = false;

    function stopAll() {
        modal.getAnimations({ subtree: true }).forEach((a) => a.cancel());
    }

    function backdrop(keyframes, options) {
        // ::backdrop con WAAPI no existe en todos los navegadores: si falla, el fondo simplemente aparece
        try { return modal.animate(keyframes, { ...options, pseudoElement: '::backdrop' }); } catch { return null; }
    }

    function animateIn() {
        stopAll();
        if (reduced()) {
            // Movimiento reducido: solo un fundido corto, sin desplazamientos
            modal.animate({ opacity: [0, 1] }, { duration: 150, easing: EASE });
            backdrop({ opacity: [0, 1] }, { duration: 150, easing: EASE });
            return;
        }
        backdrop({ opacity: [0, 1] }, { duration: 400, easing: EASE });
        modal.animate({
            opacity: [0, 1],
            transform: ['translateY(24px) scale(0.96)', 'none'],
        }, { duration: 400, easing: EASE });

        fotos.firstElementChild?.animate(
            { transform: ['scale(1.08)', 'none'] },
            { duration: 500, easing: EASE },
        );
        staggered().forEach((el, i) => el.animate({
            opacity: [0, 1],
            transform: ['translateY(12px)', 'none'],
        }, { duration: 350, delay: 120 + i * 50, easing: EASE, fill: 'backwards' }));
    }

    async function closeModal() {
        if (closing || !modal.open) return;
        closing = true;
        stopAll();
        const d = reduced() ? 120 : 200;
        const out = modal.animate({
            opacity: [1, 0],
            transform: reduced() ? ['none', 'none'] : ['none', 'translateY(12px) scale(0.97)'],
        }, { duration: d, easing: EASE, fill: 'forwards' });
        backdrop({ opacity: [1, 0] }, { duration: d, easing: EASE, fill: 'forwards' });
        await out.finished.catch(() => {});
        if (!closing) return; // se volvió a abrir mientras salía
        modal.close();
        stopAll();
    }

    modal.addEventListener('close', () => {
        closing = false;
        if (syncUrl) history.replaceState(null, '', base + location.search);
        document.body.style.overflow = '';
        window.lenis?.start();
        lastFocus?.focus();
    });

    // ── Compartir: menú nativo del móvil o, si no existe, copia el enlace /rutas/{slug} ──
    const share = document.getElementById('rmShare');
    const shareLabel = share.querySelector('[data-share-label]');
    let shareTimer = 0;
    function resetShare() { clearTimeout(shareTimer); shareLabel.textContent = 'Compartir'; }

    share.addEventListener('click', async () => {
        const url = `${base}/${slugActual}`;
        const title = document.getElementById('rmTitulo').textContent;
        if (navigator.share) {
            try { await navigator.share({ title, url }); } catch { /* la persona canceló */ }
            return;
        }
        try {
            await navigator.clipboard.writeText(url);
            shareLabel.textContent = 'Enlace copiado';
        } catch {
            shareLabel.textContent = 'No se pudo copiar';
        }
        clearTimeout(shareTimer);
        shareTimer = setTimeout(resetShare, 2000);
    });

    // Escape: en vez del cierre seco del navegador, la misma salida animada
    modal.addEventListener('cancel', (e) => { e.preventDefault(); closeModal(); });
    document.getElementById('rmClose').addEventListener('click', closeModal);
    // Clic fuera de la ventana (en el fondo oscurecido) también cierra
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    // ── Carrusel: el desplazamiento es scroll-snap nativo; aquí solo flechas, teclado y contador ──
    const slides = () => fotos.children.length;
    const current = () => Math.round(fotos.scrollLeft / (fotos.clientWidth || 1));
    const smooth = () => (matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth');

    function go(i) {
        const n = slides();
        fotos.scrollTo({ left: ((i + n) % n) * fotos.clientWidth, behavior: smooth() });
    }

    let shown = 0;
    function updateCarousel() {
        const n = slides();
        const i = current();
        count.textContent = n ? `${i + 1} / ${n}` : '';
        prev.hidden = next.hidden = n < 2;

        // La foto que llega se asienta con un leve zoom (decorativo: no bloquea nada)
        if (i !== shown && !reduced()) {
            fotos.children[i]?.querySelector('img')?.animate(
                { transform: ['scale(1.05)', 'none'] },
                { duration: 500, easing: EASE },
            );
        }
        shown = i;
    }

    prev.addEventListener('click', () => go(current() - 1));
    next.addEventListener('click', () => go(current() + 1));
    let raf = 0;
    fotos.addEventListener('scroll', () => { cancelAnimationFrame(raf); raf = requestAnimationFrame(updateCarousel); }, { passive: true });

    // Flechas del teclado, salvo cuando se está escribiendo en el formulario
    modal.addEventListener('keydown', (e) => {
        if (e.target.closest('input, select, textarea')) return;
        if (e.key === 'ArrowRight') { e.preventDefault(); go(current() + 1); }
        if (e.key === 'ArrowLeft') { e.preventDefault(); go(current() - 1); }
    });
    document.querySelectorAll('[data-ruta]').forEach((btn) => {
        btn.addEventListener('click', () => open(btn.dataset.ruta, btn));
    });

    // ── Errores por campo ──
    form.addEventListener('input', (e) => clearError(e.target.name));

    function errorEl(name) { return form.querySelector(`[data-error="${name}"]`); }

    function clearError(name) {
        const el = errorEl(name);
        if (el) { el.hidden = true; el.textContent = ''; }
        form.elements[name]?.removeAttribute?.('aria-invalid');
    }

    function clearAll() {
        form.querySelectorAll('[data-error]').forEach((el) => clearError(el.dataset.error));
    }

    function showErrors(errors) {
        let first = null;
        for (const [name, messages] of Object.entries(errors)) {
            const el = errorEl(name);
            const field = form.elements[name];
            if (el) { el.textContent = messages[0]; el.hidden = false; }
            if (field?.setAttribute) field.setAttribute('aria-invalid', 'true');
            first ??= field?.focus ? field : null;
        }
        first?.focus();
    }

    function showGeneral(msg) {
        general.textContent = msg;
        general.hidden = false;
    }

    // ── Envío ──
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        general.hidden = true;
        clearAll();

        const text = submit.textContent;
        submit.disabled = true;
        submit.textContent = 'Abriendo WhatsApp…';

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json().catch(() => ({}));

            if (res.ok) {
                if (data.whatsapp) wa.href = data.whatsapp;
                form.reset();
                form.hidden = true;
                ok.hidden = false;
                ok.focus();
                // En la misma pestaña: tras un await, window.open lo bloquean los navegadores.
                // En móvil abre la app; si no se abre, queda el botón "Abrir WhatsApp".
                if (data.whatsapp) window.location.href = data.whatsapp;
                return;
            }

            if (res.status === 422 && data.errors) showErrors(data.errors);
            else if (res.status === 429) showGeneral('Llegaron varias solicitudes seguidas. Espera un minuto y vuelve a intentarlo.');
            else if (res.status === 419) showGeneral('La página llevaba mucho tiempo abierta. Recárgala y vuelve a enviar.');
            else showGeneral('Algo falló. Inténtalo de nuevo o escríbeme directo por WhatsApp.');
        } catch {
            showGeneral('Sin conexión. Revisa tu internet e inténtalo de nuevo.');
        } finally {
            submit.disabled = false;
            submit.textContent = text;
        }
    });

    // /rutas/{slug}: el enlace compartido abre directamente el popup de esa ruta
    if (modal.dataset.open) open(modal.dataset.open, null);
}
