@props(['photo', 'sizes' => '100vw', 'loading' => 'lazy', 'imgClass' => '', 'priority' => false])

{{-- Foto responsive: AVIF con WebP de respaldo. El color dominante hace de fondo mientras carga. --}}
@php
    $srcset = fn ($ext) => collect($photo['widths'])->map(fn ($w) => "/img/web/{$photo['id']}-{$w}.{$ext} {$w}w")->implode(', ');
    $fallback = $photo['widths'][min(1, count($photo['widths']) - 1)];
@endphp
<picture {{ $attributes->merge(['style' => "background-color: {$photo['color']};"]) }}>
    <source type="image/avif" srcset="{{ $srcset('avif') }}" sizes="{{ $sizes }}">
    <img src="/img/web/{{ $photo['id'] }}-{{ $fallback }}.webp"
         srcset="{{ $srcset('webp') }}" sizes="{{ $sizes }}"
         width="{{ $photo['w'] }}" height="{{ $photo['h'] }}"
         alt="{{ $photo['alt'] }}"
         loading="{{ $priority ? 'eager' : $loading }}" decoding="async"
         @if ($priority) fetchpriority="high" @endif
         class="{{ $imgClass }}">
</picture>
