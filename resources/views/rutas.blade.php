@php
    $ruta = $abrir ? ($rutas[$abrir] ?? null) : null;
    $title = $abrir === 'a-medida' ? 'Ruta a medida por Bolivia · MarKual'
        : ($ruta ? "{$ruta['titulo']} · Rutas por Bolivia · MarKual" : 'Rutas por Bolivia · MarKual');
    $description = $ruta
        ? "{$ruta['resumen']} {$ruta['duracion']}, nivel ".mb_strtolower($ruta['nivel']).'. Pide fechas y propuesta por WhatsApp.'
        : 'Salar de Uyuni, Huayna Potosí, Sajama, la Amazonía y más. Rutas por Bolivia con el fotógrafo Marco. Pide fechas y propuesta por WhatsApp.';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @include('partials.meta', [
        'canonical' => $abrir ? route('rutas', $abrir) : route('rutas'),
        'image' => $p->ogImage($ruta['foto'] ?? 'A079'),
    ])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:300,400,500&display=swap" rel="stylesheet"/>
    <script>
        if (matchMedia('(prefers-reduced-motion: no-preference)').matches) document.documentElement.classList.add('motion-ok');
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper">

    <div id="page">

    <x-nav home="/" theme="light" actual="/rutas" :soloEscritorio="['/#archivo']" :links="[
        '/' => 'Inicio', '/#archivo' => 'Archivo', '/rutas' => 'Rutas', '/#contacto' => 'Contacto',
    ]" />

    <main>
        @include('partials.rutas', ['standalone' => true])
    </main>

    @include('partials.footer')

    </div>{{-- /#page --}}

</body>
</html>
