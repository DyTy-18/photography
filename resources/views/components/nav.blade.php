@props(['links', 'home' => '/', 'theme' => 'dark', 'soloEscritorio' => [], 'actual' => null])

{{-- Barra principal. En la portada cambia de tinta según la banda que tiene debajo (data-nav, script en welcome). --}}
<nav id="mainNav" aria-label="Principal" data-theme="{{ $theme }}"
     class="group fixed inset-x-0 top-0 z-[200] flex h-[66px] items-center justify-between border-b border-transparent px-4 text-paper transition-[color,background-color,border-color] duration-300 ease-out md:grid-cols-3 md:px-10 data-[theme=light]:border-rule-light data-[theme=light]:bg-paper/85 data-[theme=light]:text-ink data-[theme=light]:backdrop-blur-md">
    <a href="{{ $home }}" aria-label="markualko, inicio" class="block">
        {{-- Logo de prueba (crema). En la barra clara se oscurece con un filtro; con una versión oscura del logo sobraría. --}}
        <picture>
            <source type="image/webp" srcset="/img/web/logo-a-reflejo-crema.webp">
            <img src="/img/web/logo-a-reflejo-crema.png" alt="markualko" width="2231" height="419"
                 class="h-6 w-auto transition-[filter] duration-300 ease-out group-data-[theme=light]:brightness-[.18] sm:h-7 md:h-8">
        </picture>
    </a>
    {{-- En móvil no caben todos los enlaces junto al logo: los de $soloEscritorio se quedan fuera --}}
    <div class="flex gap-4 text-caption whitespace-nowrap sm:gap-8">
        @foreach ($links as $href => $text)
            <a href="{{ $href }}" @if ($href === $actual) aria-current="page" @endif
               @class(['py-3 transition-opacity duration-200 ease-out hover:opacity-60', 'hidden sm:block' => in_array($href, $soloEscritorio)])>{{ $text }}</a>
        @endforeach
    </div>
</nav>
