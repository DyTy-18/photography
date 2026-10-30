<?php

// Storyboard de la página: capítulos en orden, con los IDs del inventario
// (resources/data/inventario.json, columna "Selección (storyboard)").
// 'captions' sobrescribe el pie de foto cuando el inventario no trae lugar o hace falta más detalle.
// 'peaks' son las altitudes de cumbre que se muestran en la subida.

return [
    'hero' => ['desktop' => 'B19', 'mobile' => 'B60'],

    'chapters' => [
        'ciudades' => [
            'title' => 'Ciudades',
            'cards' => ['A014', 'B03', 'A015', 'A031', 'A066'],
            'zoom' => 'A064', // transición cenital (zoom-in)
        ],
        'lago' => [
            'title' => 'Lago',
            'photos' => ['A013', 'A051'],
        ],
        'amazonia' => [
            'title' => 'Amazonía',
            // filas de la rejilla rotada
            'rows' => [['B35', 'C08', 'C11', 'C13'], ['C21', 'C27', 'C28'], ['C37', 'C44', 'C47']],
        ],
        'altiplano' => [
            'title' => 'Altiplano',
            'band' => 'A087',   // mar de nubes, parallax a sangre
            'layers' => ['B31', 'A105', 'A116', 'A121'],
        ],
        'panoramica' => [
            'title' => 'Sajama',
            'pano' => 'B25',
        ],
        'lagunas' => [
            'title' => 'Lagunas',
            'photos' => ['B22', 'A096', 'A098'],
        ],
        'cumbre' => [
            'title' => 'Subida a la cumbre',
            // orden de ascenso
            'stack' => ['B66', 'B23', 'B20', 'B49', 'B13', 'B09'],
        ],
        'salar' => [
            'title' => 'Salar',
            'zoom' => 'A083', // laberinto de sal, transición cenital
            'photos' => ['A072', 'A075', 'A078'],
        ],
    ],

    'cta' => 'A076',

    'captions' => [
        'A031' => 'Ciudad desde el dron',
        'A066' => 'Ciudad desde el dron',
        'A064' => 'Vista cenital',
        'B66' => 'Campo alto · Huayna Potosí',
        'B23' => 'Nido de Cóndores · Illimani',
        'B20' => 'Llegando a la cima · Illimani',
        'B49' => 'Amanecer en la cima · Huayna Potosí',
        'B13' => 'Cima · Sajama',
        'B09' => 'Cima · Illimani',
        'A072' => 'Salar',
        'A075' => 'Salar',
        'A078' => 'Salar',
    ],

    'peaks' => [
        'B49' => '6.088 m',
        'B13' => '6.542 m',
        'B09' => '6.438 m',
    ],
];
