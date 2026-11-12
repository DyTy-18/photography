<?php

// Datos de contacto y captura de leads.
// WHATSAPP_NUMBER: número completo sin + ni espacios (ej. 59170000000).

return [
    'whatsapp' => env('WHATSAPP_NUMBER', 'XXXXXXXXXXX'),

    // Parámetros de campaña que se guardan con cada solicitud (primer contacto de la sesión)
    'utm' => ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'],
];
