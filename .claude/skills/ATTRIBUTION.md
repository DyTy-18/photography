# Atribución de skills

Todas las skills de esta carpeta vienen de terceros. Llegaron a través de
[claude-webkit](https://github.com/Hainrixz/claude-webkit) (MIT, Copyright (c) 2026
Enrique Rocha / Tododeia), copiadas desde el commit
`b624d8244ff075547cdda9ce3d4523440665a18c` (2026-10-25).

- Licencia de claude-webkit: `claude-webkit-LICENSE.txt`
- Aviso original de claude-webkit: `claude-webkit-NOTICE.txt`

Los comentarios `<!-- Copyright (c) 2026 Enrique Rocha ... id er-67c88ab3 -->` que
claude-webkit metió en cada `SKILL.md` se mantienen. La licencia MIT pide conservar el
aviso de copyright y la licencia en las copias; eso se cumple con esta carpeta. No se
pone crédito en el sitio público (el footer de la web no lleva "Built with...").

## Skills incluidas

| Skill | Origen | Licencia | Cambios locales |
|---|---|---|---|
| `frontend-design` | Anthropic | Apache 2.0, ver `frontend-design/NOTICE.md` | Ninguno. En movimiento manda `animate`, no `reference/motion-design.md` (ver CLAUDE.md). |
| `humanizer` | Procedencia no registrada en claude-webkit | Sin indicar | Ninguno. |
| `seo-audit` | Procedencia no registrada en claude-webkit | Sin indicar | Ninguno. Menciona skills (`programmatic-seo`, `schema-markup`, `ai-seo`) que no están instaladas. |
| `vercel-react-best-practices` | Vercel | MIT (frontmatter) | Ninguno. |
| `web-design-guidelines` | Vercel | Sin indicar | Ninguno. Descarga sus reglas de `raw.githubusercontent.com/vercel-labs/web-interface-guidelines` en cada uso. |
| `animate` | Emil Kowalski, [emilkowalski/skills](https://github.com/emilkowalski/skills) `e879241` | MIT, `LICENSE.txt` | **Sí, 1 línea (M2).** Ver abajo. |
| `emil-design-eng` | Emil Kowalski, mismo repo | MIT, `LICENSE.txt` | **Sí (M1, de claude-webkit).** Se quitó el "saluda y espera". Comentario actualizado. |
| `review-animations` | Emil Kowalski, mismo repo | MIT, `LICENSE.txt` | Ninguno. `disable-model-invocation: true` se mantiene. |
| `find-animation-opportunities` | Emil Kowalski, mismo repo | MIT, `LICENSE.txt` | **Sí, 2 líneas.** El traspaso a `improve-animations` (no instalada) ahora apunta a `animate`. |

### Cambios en detalle

**M1, `emil-design-eng/SKILL.md`** (heredado de claude-webkit). El original pedía responder
a una invocación vacía con un saludo y esperar. Se reemplazó por una sección
`## Attribution` que conserva el crédito a Emil y el enlace a animations.dev.

**M2, `animate/SKILL.md`.** El original dice "detente e invoca `pick-ui-library`";
claude-webkit lo cambió a `shadcn-ui`. Aquí no usamos ninguna de las dos, así que la línea
dice que se construya sobre los elementos nativos (`<dialog>`, `<details>`, `popover`) con
foco y teclado, y luego se anime.

**`find-animation-opportunities/SKILL.md`.** Las dos menciones a
`improve-animations plan <...>` como siguiente paso ahora apuntan a `animate`.

## Descartadas a propósito

`vercel-deploy` (desplegamos con Docker en Dokploy), `shadcn-ui` y `building-components`
(no usamos shadcn), `apple-design` (recomienda fuentes del sistema), `ui-ux-pro-max` (ya
instalada a nivel global), `playwright-cli`, `chrome-bridge-automation`, `web-reader`,
`deep-research`, `prototype`, `improve-animations`, `animation-vocabulary`.

Tampoco se copió `settings.local.json` de claude-webkit (permitía `rm`, `curl`, `git`
sin preguntar).
