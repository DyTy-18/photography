<?php

// Rutas que Marco ofrece para viajar con él. Sin precios: el formulario pide cotización.
// 'foto' (portada de la tarjeta) y 'fotos' (galería del popup) son IDs del inventario
// (resources/data/inventario.json) con versión web.
// TODO(cliente): confirmar nombres, duraciones y nivel de cada ruta con Marco.

return [
    'salar' => [
        'titulo' => 'Salar de Uyuni',
        'zona' => 'Potosí',
        'duracion' => '3 días',
        'nivel' => 'Fácil',
        'resumen' => 'El salar al amanecer, islas de cactus y las lagunas de colores camino a la frontera.',
        'foto' => 'A079',
        'fotos' => ['A068', 'A069', 'A073', 'A077', 'A081'],
    ],
    'huayna-potosi' => [
        'titulo' => 'Huayna Potosí',
        'zona' => 'La Paz · 6.088 m',
        'duracion' => '3 días',
        'nivel' => 'Exigente',
        'resumen' => 'Primer seismil para muchos. Noche en campo alto y cumbre al salir el sol.',
        'foto' => 'B10',
        'fotos' => ['B00', 'B48', 'B16', 'B53', 'B54'],
    ],
    'condoriri' => [
        'titulo' => 'Tuni Condoriri',
        'zona' => 'La Paz',
        'duracion' => '1–2 días',
        'nivel' => 'Moderado',
        'resumen' => 'Caminata entre lagunas glaciares hasta el pie del Cóndor.',
        'foto' => 'B21',
        'fotos' => ['A097', 'A099', 'A100', 'B29', 'A101'],
    ],
    'sajama' => [
        'titulo' => 'Parque Nacional Sajama',
        'zona' => 'Oruro',
        'duracion' => '2–3 días',
        'nivel' => 'Moderado',
        'resumen' => 'Géiseres, aguas termales, bofedales con vicuñas y el nevado más alto de Bolivia.',
        'foto' => 'A107',
        'fotos' => ['A104', 'A108', 'A109', 'A110', 'B50'],
    ],
    'amazonia' => [
        'titulo' => 'Pampas del Yacuma',
        'zona' => 'Rurrenabaque · Beni',
        'duracion' => '3 días',
        'nivel' => 'Fácil',
        'resumen' => 'En bote por el río, entre caimanes, delfines rosados y atardeceres sobre la pampa.',
        'foto' => 'C07',
        'fotos' => ['C06', 'C10', 'C12', 'C22', 'C23'],
    ],
    'illimani' => [
        'titulo' => 'Illimani',
        'zona' => 'La Paz · 6.438 m',
        'duracion' => '4–5 días',
        'nivel' => 'Muy exigente',
        'resumen' => 'La montaña que se ve desde toda La Paz. Nido de Cóndores y cumbre de madrugada.',
        'foto' => 'B18',
        'fotos' => ['B73', 'B74', 'B56', 'B17', 'A084'],
    ],
];
