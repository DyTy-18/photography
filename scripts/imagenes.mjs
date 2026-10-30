// Genera las versiones web de las fotos (AVIF + WebP en varios anchos) y
// resources/data/photos.json con lo que la vista necesita de cada una.
//
// Uso: node scripts/imagenes.mjs
// Origen: resources/data/inventario.json (ver scripts/inventario-a-json.py)
// Salida: public/img/web/<ID>-<ancho>.avif|webp. Es incremental: no rehace lo que ya existe.

import fs from 'node:fs/promises';
import path from 'node:path';
import sharp from 'sharp';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname.replace(/^\/(\w:)/, '$1')), '..');
const OUT = path.join(ROOT, 'public', 'img', 'web');
const inventario = JSON.parse(await fs.readFile(path.join(ROOT, 'resources', 'data', 'inventario.json'), 'utf8'));

const STORY_WIDTHS = [640, 1280, 1920, 2560];
const ARCHIVE_WIDTHS = [480, 960, 1600];
const PANO_HEIGHTS = [1000, 2000]; // la panorámica se recorre en horizontal: manda la altura

// Qué se publica: el storyboard entero y, en el archivo, todo lo demás menos duplicados y descartes
const story = inventario.filter(p => p.storyboard);
const archive = inventario.filter(p => !p.storyboard && !p.duplicate_of && !p.discard);

await fs.mkdir(OUT, { recursive: true });

const exists = f => fs.access(f).then(() => true, () => false);
const hex = ({ r, g, b }) => '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('');

async function processPhoto(p, kind) {
    const src = path.join(ROOT, p.source);
    const meta = await sharp(src).rotate().toBuffer({ resolveWithObject: true }).then(r => r.info);
    const { width: w, height: h } = meta;
    const isPano = kind === 'story' && p.orientation === 'Panorámica' && p.storyboard === 6;

    let widths;
    if (isPano) {
        widths = PANO_HEIGHTS.filter(hh => hh <= h).map(hh => Math.round(w * hh / h));
    } else {
        const base = kind === 'story' ? STORY_WIDTHS : ARCHIVE_WIDTHS;
        widths = base.filter(x => x < w);
        if (!widths.length || w < base.at(-1)) widths.push(Math.min(w, base.at(-1)));
        widths = [...new Set(widths)];
    }

    for (const width of widths) {
        const base = path.join(OUT, `${p.id}-${width}`);
        const pipeline = () => sharp(src, { limitInputPixels: false }).rotate().resize({ width, withoutEnlargement: true });
        if (!(await exists(base + '.webp'))) await pipeline().webp({ quality: 78 }).toFile(base + '.webp');
        if (!(await exists(base + '.avif'))) await pipeline().avif({ quality: 52, effort: 4 }).toFile(base + '.avif');
    }

    const { dominant } = await sharp(src).rotate().resize(64).stats();
    return { id: p.id, w, h, widths, color: hex(dominant) };
}

// Pocas en paralelo: los originales del dron pesan hasta 30 MB
const jobs = [...story.map(p => [p, 'story']), ...archive.map(p => [p, 'archive'])];
const result = {};
let done = 0;
async function worker() {
    while (jobs.length) {
        const [p, kind] = jobs.shift();
        try {
            result[p.id] = await processPhoto(p, kind);
        } catch (e) {
            console.error(`✗ ${p.id} ${p.file}: ${e.message}`);
        }
        if (++done % 10 === 0) console.log(`${done}/${story.length + archive.length}`);
    }
}
await Promise.all(Array.from({ length: 3 }, worker));

await fs.writeFile(path.join(ROOT, 'resources', 'data', 'photos.json'), JSON.stringify(result, null, 1));
console.log(`Listo: ${Object.keys(result).length} fotos (${story.length} storyboard, ${archive.length} archivo)`);
