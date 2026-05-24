from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.units import cm
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, HRFlowable
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.enums import TA_LEFT, TA_CENTER

OUTPUT = "/home/user/photography/tareas_marco.pdf"

doc = SimpleDocTemplate(
    OUTPUT,
    pagesize=A4,
    rightMargin=2*cm, leftMargin=2*cm,
    topMargin=2*cm, bottomMargin=2*cm
)

# ── Colores
NEGRO    = colors.HexColor("#0a0a0a")
TERR     = colors.HexColor("#c4571f")
OCHRE    = colors.HexColor("#d4903a")
GRIS     = colors.HexColor("#4a4a4a")
GRIS_L   = colors.HexColor("#f2f2f2")
GRIS_M   = colors.HexColor("#d8d8d8")
BLANCO   = colors.white

# ── Estilos
styles = getSampleStyleSheet()

titulo = ParagraphStyle("titulo",
    fontName="Helvetica-Bold", fontSize=20,
    textColor=NEGRO, spaceAfter=4, leading=24)

subtitulo = ParagraphStyle("subtitulo",
    fontName="Helvetica", fontSize=11,
    textColor=TERR, spaceAfter=2)

seccion = ParagraphStyle("seccion",
    fontName="Helvetica-Bold", fontSize=12,
    textColor=TERR, spaceBefore=18, spaceAfter=8, leading=16)

body = ParagraphStyle("body",
    fontName="Helvetica", fontSize=9,
    textColor=GRIS, leading=14, spaceAfter=4)

nota = ParagraphStyle("nota",
    fontName="Helvetica-Oblique", fontSize=8,
    textColor=GRIS, leading=12, spaceAfter=6)

header_cell = ParagraphStyle("hcell",
    fontName="Helvetica-Bold", fontSize=8,
    textColor=BLANCO, leading=11)

body_cell = ParagraphStyle("bcell",
    fontName="Helvetica", fontSize=8,
    textColor=NEGRO, leading=11)

small_cell = ParagraphStyle("scell",
    fontName="Helvetica", fontSize=7.5,
    textColor=GRIS, leading=10)


def h(text): return Paragraph(text, header_cell)
def b(text): return Paragraph(text, body_cell)
def s(text): return Paragraph(text, small_cell)

TBL = lambda cols, data, col_widths: Table(
    [[h(c) for c in cols]] + data,
    colWidths=col_widths,
    repeatRows=1
)

def estilo_tabla(accent=TERR):
    return TableStyle([
        ("BACKGROUND",  (0,0), (-1,0),  accent),
        ("ROWBACKGROUNDS",(0,1),(-1,-1),[BLANCO, GRIS_L]),
        ("GRID",        (0,0), (-1,-1), 0.4, GRIS_M),
        ("TOPPADDING",  (0,0), (-1,-1), 5),
        ("BOTTOMPADDING",(0,0),(-1,-1), 5),
        ("LEFTPADDING", (0,0), (-1,-1), 6),
        ("RIGHTPADDING",(0,0),(-1,-1), 6),
        ("VALIGN",      (0,0), (-1,-1), "MIDDLE"),
    ])


story = []

# ── ENCABEZADO
story.append(Paragraph("Tareas pendientes", titulo))
story.append(Paragraph("Marco @markualko — Plataforma de Fotografía Bolivia", subtitulo))
story.append(HRFlowable(width="100%", thickness=1.5, color=TERR, spaceAfter=14))
story.append(Paragraph(
    "Este documento lista todo lo que Marco debe proveer para que la plataforma quede completa y real. "
    "Los textos marcados con ★ requieren revisión y aprobación antes de publicar.",
    body))
story.append(Spacer(1, 10))


# ────────────────────────────────────────────────
# 1. FICHAS DE IMÁGENES
# ────────────────────────────────────────────────
story.append(Paragraph("1. Ficha por imagen", seccion))
story.append(Paragraph(
    "Para cada foto y video, Marco debe completar los siguientes campos. "
    "Las coordenadas GPS ya están cargadas — solo necesitan verificarse.",
    body))

imgs = [
    "IMG_0827.JPEG","IMG_0963.JPEG","IMG_0980.JPEG","IMG_1075.JPEG",
    "IMG_1187.JPEG","IMG_1269.JPEG","IMG_1362.JPG", "IMG_1373.JPEG",
    "IMG_1582.JPEG","IMG_1585.JPEG","IMG_2101.JPG", "IMG_2225.JPG",
    "IMG_4130.JPG", "IMG_4131.JPG", "IMG_4321.JPG", "IMG_4328.JPEG",
    "IMG_4356.JPEG","IMG_4366.JPEG","IMG_5098.JPEG","IMG_5114.JPEG",
    "IMG_5114.MOV", "IMG_5120.JPEG","IMG_5120.MOV", "IMG_5144.JPEG",
    "IMG_5297.JPG", "IMG_5645.JPEG","IMG_5806.JPG", "IMG_6037.JPEG",
    "IMG_6047.JPEG","IMG_6079.JPEG","IMG_7278.JPG", "IMG_7727.JPEG",
    "IMG_8382.JPG",
]

cols_img = ["Archivo", "Título real", "Lugar exacto", "Depto.", "Captura", "Fecha", "En venta"]
widths_img = [3.2*cm, 3.5*cm, 3.5*cm, 2*cm, 2*cm, 2.2*cm, 1.8*cm]

rows_img = []
for img in imgs:
    rows_img.append([
        s(img),
        b(""),
        b(""),
        b(""),
        b(""),
        b(""),
        b(""),
    ])

tbl_img = Table(
    [[h(c) for c in cols_img]] + rows_img,
    colWidths=widths_img,
    repeatRows=1
)
tbl_img.setStyle(estilo_tabla(TERR))
story.append(tbl_img)

story.append(Spacer(1, 6))
story.append(Paragraph(
    "Captura: indicar si es Celular o Dron. "
    "En venta: marcar Sí / No / Exclusivo.",
    nota))

story.append(Spacer(1, 14))


# ────────────────────────────────────────────────
# 2. PRECIOS Y LICENCIAS
# ────────────────────────────────────────────────
story.append(Paragraph("2. Precios de licencias", seccion))
story.append(Paragraph(
    "Marco define el precio final para cada tipo de licencia. El rango orientativo está en el Marco Teórico.",
    body))

cols_lic = ["Licencia", "Uso", "Precio (USD)", "Incluye video .MOV"]
widths_lic = [3*cm, 7*cm, 3*cm, 4.3*cm]

rows_lic = [
    [b("Personal"),   s("Impresión personal, decoración, redes propias"), b(""), b("")],
    [b("Editorial"),  s("Medios, libros, revistas — con atribución"),      b(""), b("")],
    [b("Comercial"),  s("Publicidad, campañas, material corporativo"),     b(""), b("")],
    [b("Packs"),      s("¿Ofrece paquetes por destino? ¿Cuántas fotos?"), b(""), b("")],
]

tbl_lic = Table([[h(c) for c in cols_lic]] + rows_lic, colWidths=widths_lic, repeatRows=1)
tbl_lic.setStyle(estilo_tabla(OCHRE))
story.append(tbl_lic)

story.append(Spacer(1, 14))


# ────────────────────────────────────────────────
# 3. TEXTOS DEL SITIO (con aprobación)
# ────────────────────────────────────────────────
story.append(Paragraph("3. Textos del sitio ★ (requieren aprobación)", seccion))
story.append(Paragraph(
    "Los textos actuales son genéricos e hipotéticos. Marco debe revisar cada uno, "
    "confirmar o reescribir, y dar el visto bueno antes de publicar.",
    body))

cols_txt = ["Sección", "Texto actual (genérico)", "Marco escribe / confirma", "Aprobado"]
widths_txt = [3*cm, 5.5*cm, 5.5*cm, 2.3*cm]

rows_txt = [
    [b("Hero — título"),
     s('"Caminos que ni sabía que existían"'),
     b(""), b("")],
    [b("Hero — países"),
     s("Bolivia · Argentina · Chile"),
     b(""), b("")],
    [b("About — frase"),
     s('"Ingeniero civil boliviano que no para de viajar"'),
     b(""), b("")],
    [b("About — bio"),
     s("Texto genérico inventado (3 líneas)"),
     b(""), b("")],
    [b("Contacto — descripción"),
     s('"Expediciones en curso, detrás de cámara..."'),
     b(""), b("")],
    [b("Licencia Personal — descripción"),
     s("No existe aún"),
     b(""), b("")],
    [b("Licencia Editorial — descripción"),
     s("No existe aún"),
     b(""), b("")],
    [b("Licencia Comercial — descripción"),
     s("No existe aún"),
     b(""), b("")],
    [b("Nombre del dominio"),
     s("markualko.com / boliviapaisajes.com (sin decidir)"),
     b(""), b("")],
    [b("Email de contacto"),
     s("No aparece en el sitio"),
     b(""), b("")],
    [b("WhatsApp"),
     s("XXXXXXXXXXX (placeholder)"),
     b(""), b("")],
]

tbl_txt = Table([[h(c) for c in cols_txt]] + rows_txt, colWidths=widths_txt, repeatRows=1)
tbl_txt.setStyle(estilo_tabla(NEGRO))
story.append(tbl_txt)

story.append(Spacer(1, 14))


# ────────────────────────────────────────────────
# 4. BLOG
# ────────────────────────────────────────────────
story.append(Paragraph("4. Blog / Relatos de campo", seccion))
story.append(Paragraph(
    "Se necesitan al menos 3 artículos para el lanzamiento. Marco redacta el contenido.",
    body))

cols_blog = ["#", "Tema sugerido", "Contenido mínimo", "Estado"]
widths_blog = [1*cm, 5*cm, 9*cm, 2.3*cm]

rows_blog = [
    [b("1"), b("Expedición a [cumbre]"),
     s("Ruta, días, equipo usado, condiciones, anécdota, fotos del viaje"), b("")],
    [b("2"), b("Amazonía boliviana"),
     s("Cómo llegar, mejor época, qué encontró, fauna, comunidades"), b("")],
    [b("3"), b("Fotografía con dron en Bolivia"),
     s("Permisos DGAC, mejores spots, tips técnicos, zonas restringidas"), b("")],
]

tbl_blog = Table([[h(c) for c in cols_blog]] + rows_blog, colWidths=widths_blog, repeatRows=1)
tbl_blog.setStyle(estilo_tabla(colors.HexColor("#7a9468")))
story.append(tbl_blog)

story.append(Spacer(1, 14))


# ────────────────────────────────────────────────
# 5. RUTAS / TRACKS
# ────────────────────────────────────────────────
story.append(Paragraph("5. Rutas de expedición (mapa)", seccion))
story.append(Paragraph(
    "Si Marco tiene archivos GPX de sus expediciones, se pueden pintar como líneas en el mapa interactivo.",
    body))

cols_ruta = ["Expedición", "Desde", "Hasta", "Fecha aprox.", "¿Tiene GPX?"]
widths_ruta = [4*cm, 4*cm, 4*cm, 3*cm, 2.3*cm]

rows_ruta = [
    [b(""), b(""), b(""), b(""), b("Sí / No")],
    [b(""), b(""), b(""), b(""), b("Sí / No")],
    [b(""), b(""), b(""), b(""), b("Sí / No")],
]

tbl_ruta = Table([[h(c) for c in cols_ruta]] + rows_ruta, colWidths=widths_ruta, repeatRows=1)
tbl_ruta.setStyle(estilo_tabla(colors.HexColor("#d4903a")))
story.append(tbl_ruta)

story.append(Spacer(1, 20))
story.append(HRFlowable(width="100%", thickness=0.5, color=GRIS_M, spaceAfter=8))
story.append(Paragraph(
    "© 2025 @markualko · Documento interno del proyecto — no distribuir",
    ParagraphStyle("footer", fontName="Helvetica", fontSize=7,
                   textColor=GRIS_M, alignment=TA_CENTER)))


doc.build(story)
print(f"PDF generado: {OUTPUT}")
