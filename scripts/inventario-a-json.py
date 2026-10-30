"""Convierte el inventario (xlsx) a resources/data/inventario.json y pasa los HEIC a JPEG.

Uso: python scripts/inventario-a-json.py RUTA/inventario_fotos_markual.xlsx

- El JSON es la fuente de datos de la página (el xlsx no vive en el repo).
- Los HEIC se convierten a storage/app/heic/<nombre>.jpg (calidad 95), porque sharp
  no trae el decodificador HEVC. scripts/imagenes.mjs usa esos JPEG como origen.
"""
import json, os, sys
import openpyxl
import pillow_heif
from PIL import Image, ImageOps

pillow_heif.register_heif_opener()

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
GALERIA = os.path.join(ROOT, 'public', 'img', 'galeria')
HEIC_OUT = os.path.join(ROOT, 'storage', 'app', 'heic')

FIELDS = {
    'ID': 'id', 'Categoría': 'category', 'Lugar': 'place', 'Toma': 'shot',
    'Carpeta original': 'folder', 'Archivo': 'file', 'Ancho (px)': 'w', 'Alto (px)': 'h',
    'Orientación': 'orientation', 'Calidad': 'quality', 'Zoom': 'zoom',
    'Full desktop': 'full_desktop', 'Full móvil': 'full_mobile', 'Marca de agua': 'watermark',
    'Duplicado de': 'duplicate_of', 'Selección (storyboard)': 'storyboard', 'Notas': 'notes',
    'Detalle (0–100)': 'detail',
}


def main(xlsx):
    ws = openpyxl.load_workbook(xlsx, data_only=True)['Inventario']
    rows = list(ws.iter_rows(values_only=True))
    head = rows[0]
    out = []
    for r in rows[1:]:
        d = {FIELDS[k]: v for k, v in zip(head, r) if k in FIELDS}
        if not d.get('id'):
            continue
        src = os.path.join(GALERIA, d['folder'], d['file'])
        if not os.path.exists(src):
            sys.exit(f"No existe: {src}")
        rel = os.path.relpath(src, ROOT).replace(os.sep, '/')
        if d['file'].lower().endswith('.heic'):
            jpg = os.path.join(HEIC_OUT, os.path.splitext(d['file'])[0] + '.jpg')
            if not os.path.exists(jpg):
                ImageOps.exif_transpose(Image.open(src)).convert('RGB').save(jpg, quality=95)
            rel = os.path.relpath(jpg, ROOT).replace(os.sep, '/')
        d['source'] = rel
        d['place'] = (d['place'] or '').strip()
        d['storyboard'] = int(str(d['storyboard']).split(' ')[0]) if d['storyboard'] else None
        d['watermark'] = d['watermark'] == 'Sí'
        d['discard'] = bool(d['notes'] and str(d['notes']).startswith('Descartar'))
        out.append(d)

    path = os.path.join(ROOT, 'resources', 'data', 'inventario.json')
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(out, f, ensure_ascii=False, indent=1)
    print(f"{len(out)} fotos -> {os.path.relpath(path, ROOT)}")


if __name__ == '__main__':
    main(sys.argv[1])
