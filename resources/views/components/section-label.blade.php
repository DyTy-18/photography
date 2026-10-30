@props(['tone' => 'light'])

{{-- Etiqueta de sección. tone = fondo sobre el que va (light = paper, dark = ink). --}}
<h2 {{ $attributes->class([
    'text-caption',
    'text-ink' => $tone === 'light',
    'text-paper' => $tone === 'dark',
]) }}>{{ $slot }}</h2>
