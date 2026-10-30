@props(['title', 'count' => null])

{{-- Encabezado de capítulo sobre fondo oscuro: título grande en peso fino y, a la derecha, un dato. --}}
<header {{ $attributes->class('flex items-end justify-between gap-6 border-b border-rule-dark pb-6') }}>
    <h2 class="text-title font-light" data-reveal>{{ $title }}</h2>
    @if ($count)
        <p class="text-caption text-gray-dark" data-reveal=".1">{{ $count }}</p>
    @endif
</header>
