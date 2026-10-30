# MarKual: Photography & Film

Portafolio de una sola página del fotógrafo Marco (@markualko): fotos y vídeo de viajes por
Bolivia, Argentina y Chile.

## Stack

- Laravel 12 (PHP 8.2+). Una sola ruta `/` en `routes/web.php` que devuelve `welcome`.
- Vista: `resources/views/welcome.blade.php` con Tailwind 4 (tokens en `resources/css/app.css`)
  y componentes en `resources/views/components/` (`picture`, `chapter`, `section-label`).
- Animaciones de scroll: GSAP 3 + ScrollTrigger y Lenis, en `resources/js/story.js`. La neblina
  de la intro es un shader WebGL propio sobre la foto del hero, en `resources/js/fog.js`.
- Vite 7 (`npm run dev` / `npm run build`). Fuente: Hanken Grotesk desde Bunny Fonts.
- Despliegue: Docker en Dokploy. No usar Vercel ni scripts que suban el código a terceros.
- No hay React ni shadcn/ui.

## Fotos

- Originales en `public/img/galeria/<categoría>/` (~800 MB). No se sirven tal cual.
- Fuente de datos: el inventario del cliente (xlsx) convertido a `resources/data/inventario.json`
  con `python scripts/inventario-a-json.py <ruta.xlsx>` (también pasa los HEIC a JPEG en
  `storage/app/heic/`, porque sharp no decodifica HEVC).
- Versiones web: `node scripts/imagenes.mjs` genera AVIF + WebP en `public/img/web/` y
  `resources/data/photos.json`. Es incremental.
- Storyboard (qué foto va en cada capítulo y con qué efecto): `config/story.php`.
  `app/Support/Portfolio.php` junta los tres. El archivo son el resto de fotos sin duplicados ni
  descartes.
- Si cambia el xlsx: volver a correr los dos scripts.

## Requisitos del proyecto

- Sin precios ni pagos por ahora. Se quitaron el modal de compra y los botones de
  Stripe/PayPal; no volver a añadirlos sin que se pida.
- Contacto = captura de leads: formulario + WhatsApp + parámetros UTM. No usar `mailto:`
  ni Formspree como solución de contacto.
- Sin créditos de plantillas o herramientas en el footer público.

## Skills del proyecto

Están en `.claude/skills/` (licencias en `.claude/skills/ATTRIBUTION.md`). Son herramientas
de conocimiento: se consultan cuando aplican, no cambian el rol ni pausan el trabajo.

| Skill | Cuándo |
|---|---|
| `frontend-design` | Diseño visual: que no se vea genérico ni hecho por IA. |
| `humanizer` | Todo texto visible en la web, antes de darlo por bueno. |
| `seo-audit` | Antes de publicar, o al tocar `<title>`, meta, Open Graph o headings. |
| `web-design-guidelines` | Revisión de UI y accesibilidad. Descarga sus reglas de GitHub al usarse. |
| `animate` | Construir una animación nueva. |
| `emil-design-eng` | Criterio de detalle de UI y movimiento. |
| `review-animations` | Revisar animaciones existentes (solo se invoca a mano). |
| `find-animation-opportunities` | Buscar dónde falta movimiento. Solo propone, no edita. |
| `vercel-react-best-practices` | Solo si algún día se introduce React. Para Blade + JS no aplica. |

`ui-ux-pro-max` está instalada a nivel global. Las skills de animación mencionan
`improve-animations`, que no está instalada: ignora esas referencias.

Documentos de apoyo: `docs/performance-checklist.md` y `docs/accessibility-checklist.md`.
Sus ejemplos de código son de Next.js/shadcn; tradúcelos a Blade + JS (ver la nota al
principio de cada uno).

## Precedencia entre skills

Cuando dos skills se contradicen, decide esta tabla. No mezcles las dos opiniones ni
presentes ambas.

| Tema | Manda | Queda por debajo |
|---|---|---|
| Movimiento: curvas, duraciones, stagger, gestos | `animate` → `emil-design-eng` | `frontend-design/reference/motion-design.md` |
| Tipografía, color, layout, evitar el "look IA" | `frontend-design` + `ui-ux-pro-max` | — |
| Accesibilidad y revisión de UI | `web-design-guidelines` + `docs/accessibility-checklist.md` | — |
| SEO | `seo-audit` | — |
| Rendimiento | `docs/performance-checklist.md` | `vercel-react-best-practices` (solo React) |
| Textos | `humanizer` | — |

## Reglas fijas de movimiento

Mandan sobre cualquier cosa que digan las skills o los documentos.

- **Nunca `ease-in` en UI**, ni al entrar ni al salir. Usa
  `--ease-out: cubic-bezier(0.23, 1, 0.32, 1)`.
- **UI por debajo de 300ms** (botón 100-160, tooltip 125-200, dropdown 150-250). Modales y
  el lightbox pueden llegar a 200-500. Las superficies de presentación (hero, revelados al
  hacer scroll) están exentas y pueden durar 500-800ms. Ten claro cuál estás animando.
- **Nunca `scale(0)`.** Empieza en `scale(0.95)` + `opacity: 0`.
- **Nunca `transition: all`.** Nombra las propiedades.
- Anima solo `transform` y `opacity` (y `clip-path`). Respeta `prefers-reduced-motion`.

## Textos y contraste

- Todo el copy visible pasa por `humanizer`. El sitio está en español neutro: tuteo ("sigue",
  "mira"), nunca voseo ("seguí", "tocá") ni modismos regionales.
- Contraste WCAG AA: 4.5:1 en texto normal, 3:1 en texto grande. Ojo con los textos
  `rgba(237,227,204,.25-.35)` sobre fondo oscuro que usa el diseño actual.
