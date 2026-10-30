@php
    $wrap = 'mx-auto w-full max-w-[1280px] px-4 md:px-10';
    $pillLight = 'inline-flex items-center gap-2 rounded-full border border-ink px-[33px] py-[11px] text-base text-ink transition-[background-color,color,scale] duration-200 ease-out hover:bg-ink hover:text-paper active:scale-[.97]';
    $c = $story['chapters'];

    $heroD = $p->photo($story['hero']['desktop']);
    $heroM = $p->photo($story['hero']['mobile']);
    $set = fn ($photo, $ext) => collect($photo['widths'])->map(fn ($w) => "/img/web/{$photo['id']}-{$w}.{$ext} {$w}w")->implode(', ');

    $archiveCats = $archive->countBy('category')->sortKeys();
    $pageSize = 24;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MarKual — Photography & Film</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:300,400,500&display=swap" rel="stylesheet"/>
    <link rel="preload" as="image" type="image/avif"
          imagesrcset="{{ $set($heroD, 'avif') }}" imagesizes="100vw" media="(min-width: 768px)">
    {{-- Antes del primer pintado: la intro empieza siempre arriba y ya en niebla (ver app.css) --}}
    <script>
        if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
        if (matchMedia('(prefers-reduced-motion: no-preference)').matches) document.documentElement.classList.add('motion-ok');
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-ink">

    <div id="page">

    <!-- NAV: cambia de tinta según la banda que tiene debajo (data-nav) -->
    <nav id="mainNav" aria-label="Principal" data-theme="dark"
         class="group fixed inset-x-0 top-0 z-[200] flex h-[66px] items-center justify-between border-b border-transparent px-4 text-paper transition-[color,background-color,border-color] duration-300 ease-out md:grid-cols-3 md:px-10 data-[theme=light]:border-rule-light data-[theme=light]:bg-paper/85 data-[theme=light]:text-ink data-[theme=light]:backdrop-blur-md">
        <a href="#inicio" aria-label="markualko, inicio" class="block">
            {{-- Logo de prueba (crema). En la barra clara se oscurece con un filtro; con una versión oscura del logo sobraría. --}}
            <picture>
                <source type="image/webp" srcset="/img/web/logo-a-reflejo-crema.webp">
                <img src="/img/web/logo-a-reflejo-crema.png" alt="markualko" width="2231" height="419"
                     class="h-6 w-auto transition-[filter] duration-300 ease-out group-data-[theme=light]:brightness-[.18] sm:h-7 md:h-8">
            </picture>
        </a>
        {{-- En móvil no caben los cuatro enlaces junto al logo: "Viaje" (la primera sección tras la intro) se queda fuera --}}
        <div class="flex gap-4 text-caption whitespace-nowrap sm:gap-8">
            @foreach (['#ciudades' => 'Viaje', '#archivo' => 'Archivo', '#sobre-mi' => 'Sobre mí', '#contacto' => 'Contacto'] as $href => $text)
                <a href="{{ $href }}" @class(['py-3 transition-opacity duration-200 ease-out hover:opacity-60', 'hidden sm:block' => $loop->first])>{{ $text }}</a>
            @endforeach
        </div>
    </nav>

    <main>

    <!-- 01 · INTRO + HERO -->
    <section id="inicio" data-intro data-nav="dark" class="relative h-svh bg-ink text-paper motion-ok:h-[180vh]">
        <div class="sticky top-0 h-svh overflow-hidden">
            <div data-intro-photo class="absolute inset-0">
                <picture style="background-color: {{ $heroD['color'] }}">
                    <source media="(max-width: 767px)" type="image/avif" srcset="{{ $set($heroM, 'avif') }}" sizes="100vw">
                    <source media="(max-width: 767px)" type="image/webp" srcset="{{ $set($heroM, 'webp') }}" sizes="100vw">
                    <source type="image/avif" srcset="{{ $set($heroD, 'avif') }}" sizes="100vw">
                    <img src="/img/web/{{ $heroD['id'] }}-{{ $heroD['widths'][1] ?? $heroD['widths'][0] }}.webp"
                         srcset="{{ $set($heroD, 'webp') }}" sizes="100vw"
                         width="{{ $heroD['w'] }}" height="{{ $heroD['h'] }}" alt="{{ $heroD['alt'] }}"
                         fetchpriority="high" decoding="async" class="absolute inset-0 size-full object-cover">
                </picture>
            </div>
            <canvas id="fog" data-intro-fog class="absolute inset-0 hidden size-full motion-ok:block" aria-hidden="true"></canvas>
            {{-- Sombra para el titular: abajo a la izquierda, donde va el texto --}}
            <div class="absolute inset-0 bg-[linear-gradient(to_top,rgb(12_12_11/.75)_0%,rgb(12_12_11/.15)_45%,transparent_70%),linear-gradient(to_bottom,rgb(12_12_11/.35)_0%,transparent_18%)]"></div>

            <h1 class="absolute bottom-0 left-0 max-w-[18ch] px-4 pb-10 text-[clamp(2.5rem,5.6vw,5.75rem)] leading-[1] font-light tracking-[-.035em] md:px-10 md:pb-14">
                <span data-intro-line class="block">Caminos que ni sabía</span>
                <span data-intro-line class="block">que existían.</span>
            </h1>
        </div>
    </section>

    <!-- 02 · CIUDADES: tarjetas con parallax interno + transición cenital -->
    <section id="ciudades" data-chapter="02 Ciudades" data-nav="dark" class="bg-ink pt-24 text-paper md:pt-38">
        @php
            $cards = $p->many($c['ciudades']['cards']);
            $columns = [[], []];
            foreach ($cards as $i => $card) { $columns[$i % 2][] = $card; }
        @endphp
        <div class="{{ $wrap }}">
            <x-chapter :title="$c['ciudades']['title']" :count="(count($cards) + 1).' fotos'" />

            <div class="mt-16 grid gap-16 md:mt-24 md:grid-cols-2 md:gap-x-8 md:gap-y-0">
                @foreach ($columns as $col => $column)
                    <div @class(['flex flex-col gap-16 md:gap-38', 'md:pt-[28vh]' => $col === 1])>
                        @foreach ($column as $photo)
                            <figure data-reveal>
                                <div data-parallax="9" class="overflow-hidden" style="aspect-ratio: {{ $photo['w'] }} / {{ $photo['h'] }}">
                                    <x-picture :photo="$photo" sizes="(min-width: 768px) 50vw, 100vw" class="block size-full" imgClass="size-full scale-[1.2] object-cover" />
                                </div>
                                <figcaption class="mt-4 flex justify-between text-caption text-gray-dark">
                                    <span class="text-paper">{{ $photo['caption'] }}</span>
                                    <span>{{ $photo['shot'] === 'Drone' ? 'Dron' : 'Terrestre' }}</span>
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        @php $zoom = $p->photo($c['ciudades']['zoom']); @endphp
        <div data-zoom class="relative mt-24 h-svh md:mt-38 motion-ok:h-[200vh]">
            <div class="sticky top-0 h-svh overflow-hidden">
                <div data-zoom-frame class="absolute inset-0 overflow-hidden">
                    <x-picture :photo="$zoom" sizes="100vw" class="block size-full" imgClass="size-full object-cover" />
                </div>
                <p data-zoom-caption class="{{ $wrap }} absolute inset-x-0 bottom-10 text-caption text-paper">{{ $zoom['caption'] }}</p>
            </div>
        </div>
    </section>

    <!-- 03 · LAGO -->
    <section id="lago" data-chapter="03 Lago" data-nav="dark" class="bg-ink py-24 text-paper md:py-38">
        @php [$lagoA, $lagoB] = $p->many($c['lago']['photos']); @endphp
        <div class="{{ $wrap }}">
            <x-chapter :title="$c['lago']['title']" :count="$lagoA['caption']" />
            <div class="mt-16 grid gap-8 md:mt-24 md:grid-cols-12">
                <figure class="md:col-span-8" data-reveal>
                    <div data-parallax="9" class="overflow-hidden" style="aspect-ratio: {{ $lagoA['w'] }} / {{ $lagoA['h'] }}">
                        <x-picture :photo="$lagoA" sizes="(min-width: 768px) 66vw, 100vw" class="block size-full" imgClass="size-full scale-[1.2] object-cover" />
                    </div>
                </figure>
                <figure class="self-end md:col-span-4 md:-mb-[18vh]" data-speed="0.12">
                    <div data-parallax="9" class="overflow-hidden" style="aspect-ratio: {{ $lagoB['w'] }} / {{ $lagoB['h'] }}">
                        <x-picture :photo="$lagoB" sizes="(min-width: 768px) 33vw, 100vw" class="block size-full" imgClass="size-full scale-[1.2] object-cover" />
                    </div>
                    <figcaption class="mt-4 text-caption text-gray-dark">{{ $lagoB['caption'] }}</figcaption>
                </figure>
            </div>
        </div>
    </section>

    <!-- 04 · AMAZONÍA: rejilla rotada -->
    <section id="amazonia" data-chapter="04 Amazonía" data-nav="dark" class="overflow-hidden bg-ink pt-24 text-paper md:pt-38">
        @php $rows = array_map(fn ($r) => $p->many($r), $c['amazonia']['rows']); @endphp
        <div class="{{ $wrap }}">
            <x-chapter :title="$c['amazonia']['title']" :count="array_sum(array_map('count', $rows)).' fotos'" />
        </div>
        <div data-tiles class="relative mt-16 h-[90vh] md:mt-24 md:h-[120vh]">
            <div class="absolute top-1/2 left-1/2 flex w-[220vw] -translate-1/2 -rotate-[14deg] flex-col gap-3 md:w-[170vw]">
                @foreach ($rows as $row)
                    <div data-tiles-row class="flex justify-center gap-3">
                        @foreach (range(0, 2) as $copy)
                            @foreach ($row as $photo)
                                <div class="aspect-[4/3] w-[44vw] shrink-0 overflow-hidden md:w-[22vw]" @if ($copy) aria-hidden="true" @endif>
                                    <x-picture :photo="$copy ? array_merge($photo, ['alt' => '']) : $photo" sizes="(min-width: 768px) 22vw, 44vw" class="block size-full" imgClass="size-full object-cover" />
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 05 · ALTIPLANO: banda a sangre + capas -->
    <section id="altiplano" data-chapter="05 Altiplano" data-nav="dark" class="bg-ink text-paper">
        @php $band = $p->photo($c['altiplano']['band']); $alt = $p->many($c['altiplano']['layers']); @endphp
        <div data-band class="relative h-[110vh] overflow-hidden">
            <x-picture :photo="$band" sizes="100vw" class="absolute inset-x-0 -top-[15%] block h-[130%]" imgClass="size-full object-cover" />
            <div class="absolute inset-0 bg-ink/35"></div>
            <div data-band-title class="absolute inset-0 grid place-items-center text-center">
                <h2 class="text-[clamp(4rem,15vw,15rem)] leading-[.9] font-light tracking-[-.04em]">
                    {{ $c['altiplano']['title'] }}
                </h2>
            </div>
            <p class="{{ $wrap }} absolute inset-x-0 bottom-10 text-caption text-paper/80">{{ $band['caption'] }}</p>
        </div>

        <div class="{{ $wrap }} grid grid-cols-12 gap-x-4 gap-y-16 py-24 md:gap-x-8 md:py-38">
            @foreach ([
                // Cada columna se mueve a una sola velocidad para que las fotos no se monten
                ['col-span-12 md:col-span-5', '0.05'],
                ['col-span-12 md:col-span-6 md:col-start-7 md:mt-[24vh]', '-0.05'],
                ['col-span-12 md:col-span-4 md:col-start-2', '0.05'],
                ['col-span-12 md:col-span-5 md:col-start-7 md:mt-[12vh]', '-0.05'],
            ] as $i => [$cls, $speed])
                @isset($alt[$i])
                    <figure class="{{ $cls }}" data-speed="{{ $speed }}">
                        <div class="overflow-hidden" style="aspect-ratio: {{ $alt[$i]['w'] }} / {{ $alt[$i]['h'] }}">
                            <x-picture :photo="$alt[$i]" sizes="(min-width: 768px) 42vw, 100vw" class="block size-full" imgClass="size-full object-cover" />
                        </div>
                        <figcaption class="mt-4 text-caption text-gray-dark">{{ $alt[$i]['caption'] }}</figcaption>
                    </figure>
                @endisset
            @endforeach
        </div>
    </section>

    <!-- SAJAMA: panorámica en banda a sangre; se desliza de lado mientras pasa -->
    @php $pano = $p->photo($c['panoramica']['pano']); $ratio = round($pano['w'] / $pano['h'], 3); @endphp
    <section id="panoramica" data-drift data-nav="dark" class="relative h-svh overflow-hidden bg-ink text-paper">
        <x-picture :photo="$pano" sizes="calc(100vh * {{ $ratio }})" data-drift-track
                   class="absolute inset-y-0 left-0 block h-full w-full motion-ok:w-(--pano-w) motion-ok:max-w-none"
                   style="--pano-w: calc(100svh * {{ $ratio }})" imgClass="size-full object-cover" />
        <div class="absolute inset-0 bg-linear-to-t from-ink/70 via-transparent via-40% to-ink/30"></div>
        <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-6 px-4 pb-10 md:px-10">
            <h2 class="text-title font-light" data-reveal>{{ $c['panoramica']['title'] }}</h2>
            <p class="text-caption text-paper/80" data-reveal=".1">Panorámica desde el dron</p>
        </div>
    </section>

    <!-- 07 · LAGUNAS: texto fijo a la izquierda, fotos con parallax a la derecha -->
    <section id="lagunas" data-chapter="07 Lagunas" data-nav="dark" class="bg-ink py-24 text-paper md:py-38">
        @php $lagunas = $p->many($c['lagunas']['photos']); @endphp
        <div class="{{ $wrap }} grid gap-16 md:grid-cols-12 md:gap-8">
            <div class="md:sticky md:top-32 md:col-span-4 md:self-start">
                <h2 class="text-title font-light" data-reveal>
                    {{ $c['lagunas']['title'] }}
                </h2>
                <ul class="mt-10 border-t border-rule-dark text-caption" data-reveal=".1">
                    @foreach ($lagunas as $photo)
                        <li class="border-b border-rule-dark py-3">{{ $photo['caption'] }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="flex flex-col gap-16 md:col-span-7 md:col-start-6 md:gap-38">
                @foreach ($lagunas as $photo)
                    <figure data-reveal>
                        <div data-parallax="10" class="overflow-hidden" style="aspect-ratio: {{ $photo['w'] }} / {{ $photo['h'] }}">
                            <x-picture :photo="$photo" sizes="(min-width: 768px) 55vw, 100vw" class="block size-full" imgClass="size-full scale-[1.22] object-cover" />
                        </div>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 08 · SUBIDA A LA CUMBRE: pila de fotos fijas -->
    <section id="cumbre" data-chapter="08 Cumbre" data-nav="dark" class="bg-ink text-paper">
        @php $stack = $p->many($c['cumbre']['stack']); @endphp
        <div class="{{ $wrap }} pt-24 pb-16 md:pt-38 md:pb-24">
            <x-chapter :title="$c['cumbre']['title']" :count="count($stack).' fotos'" />
        </div>
        @foreach ($stack as $photo)
            <article data-stack-card class="sticky top-0 h-svh overflow-hidden">
                <div data-stack-inner class="relative size-full origin-top bg-ink">
                    <x-picture :photo="$photo" sizes="100vw" class="absolute inset-0 block" imgClass="size-full object-cover" />
                    {{-- El texto va arriba: la foto siguiente entra desde abajo y lo taparía --}}
                    <div class="absolute inset-0 bg-linear-to-b from-ink/75 via-ink/10 via-35% to-transparent"></div>
                    <div class="absolute inset-x-0 top-0 flex items-start justify-between gap-6 px-4 pt-24 md:px-10 md:pt-28">
                        <h3 class="max-w-[18ch] text-statement font-light">{{ $photo['caption'] }}</h3>
                        @if ($photo['peak'])
                            <p data-peak="{{ $photo['peak'] }}" class="shrink-0 text-[clamp(2rem,5.5vw,5.5rem)] leading-none font-light tracking-[-.04em] tabular-nums">{{ $photo['peak'] }}</p>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <!-- 09 · SALAR (clímax): transición cenital + manifiesto + tríptico -->
    <section id="salar" data-chapter="09 Salar" data-nav="dark" class="bg-ink text-paper">
        @php $salZoom = $p->photo($c['salar']['zoom']); $salar = $p->many($c['salar']['photos']); @endphp
        <div class="{{ $wrap }} pt-24 pb-16 md:pt-38 md:pb-24">
            <x-chapter :title="$c['salar']['title']" :count="(count($salar) + 1).' fotos'" />
        </div>
        <div data-zoom class="relative h-svh motion-ok:h-[200vh]">
            <div class="sticky top-0 h-svh overflow-hidden">
                <div data-zoom-frame class="absolute inset-0 overflow-hidden">
                    <x-picture :photo="$salZoom" sizes="100vw" class="block size-full" imgClass="size-full object-cover" />
                </div>
                <p data-zoom-caption class="{{ $wrap }} absolute inset-x-0 bottom-10 text-caption text-paper">{{ $salZoom['caption'] }}</p>
            </div>
        </div>

        <div class="{{ $wrap }} py-24 md:py-38" data-lines>
            <p class="text-[clamp(2.5rem,7.5vw,7.5rem)] leading-[1] font-light tracking-[-.035em]">
                <span class="block overflow-hidden pb-[.1em]"><span data-line class="block">El 2025 me mostró</span></span>
                <span class="block overflow-hidden pb-[.1em]"><span data-line class="block text-gray-dark">caminos que ni sabía</span></span>
                <span class="block overflow-hidden pb-[.1em]"><span data-line class="block">que existían.</span></span>
            </p>
        </div>

        <div class="{{ $wrap }} grid grid-cols-12 items-start gap-4 pb-24 md:gap-8 md:pb-38">
            @foreach ($salar as $photo)
                <figure @class(['col-span-12 md:col-span-4', 'md:mt-[16vh]' => $loop->iteration === 2]) data-speed="{{ [0.06, -0.08, 0.12][$loop->index] }}">
                    <div data-parallax="8" class="overflow-hidden" style="aspect-ratio: {{ $photo['w'] }} / {{ $photo['h'] }}">
                        <x-picture :photo="$photo" sizes="(min-width: 768px) 33vw, 100vw" class="block size-full" imgClass="size-full scale-[1.2] object-cover" />
                    </div>
                </figure>
            @endforeach
        </div>
    </section>

    <!-- ARCHIVO -->
    <section id="archivo" data-nav="light" class="bg-paper py-24 text-ink md:py-38">
        <div class="{{ $wrap }}">
            <div class="flex items-baseline justify-between">
                <x-section-label>Archivo</x-section-label>
                <p class="text-caption text-gray-light">{{ $archive->count() }} fotos</p>
            </div>
            <p class="mt-10 max-w-[22ch] text-statement font-light" data-reveal>El resto del archivo, por categoría.</p>

            <div class="mt-12 flex flex-wrap gap-2" role="group" aria-label="Filtrar por categoría">
                <button type="button" data-archive-filter="all" aria-pressed="true"
                        class="inline-flex items-baseline gap-2 rounded-full border border-ink/20 px-5 py-2.5 text-caption transition-[color,background-color,border-color,scale] duration-200 ease-out hover:border-ink active:scale-[.97] aria-pressed:border-ink aria-pressed:bg-ink aria-pressed:text-paper">
                    Todo<span class="tabular-nums opacity-70">{{ $archive->count() }}</span>
                </button>
                @foreach ($archiveCats as $cat => $n)
                    <button type="button" data-archive-filter="{{ $cat }}" aria-pressed="false"
                            class="inline-flex items-baseline gap-2 rounded-full border border-ink/20 px-5 py-2.5 text-caption transition-[color,background-color,border-color,scale] duration-200 ease-out hover:border-ink active:scale-[.97] aria-pressed:border-ink aria-pressed:bg-ink aria-pressed:text-paper">
                        {{ $cat }}<span class="tabular-nums opacity-70">{{ $n }}</span>
                    </button>
                @endforeach
            </div>

            <div id="archiveGrid" class="mt-12 columns-2 gap-3 md:columns-3 lg:columns-4">
                @foreach ($archive as $photo)
                    <button type="button" data-archive-item data-cat="{{ $photo['category'] }}"
                            data-full="/img/web/{{ $photo['id'] }}-{{ last($photo['widths']) }}.webp"
                            data-caption="{{ $photo['caption'] }}" data-alt="{{ $photo['alt'] }}"
                            aria-label="Ampliar: {{ $photo['caption'] }}"
                            @if ($loop->index >= $pageSize) hidden @endif
                            class="group mb-3 block w-full cursor-zoom-in break-inside-avoid overflow-hidden"
                            style="aspect-ratio: {{ $photo['w'] }} / {{ $photo['h'] }}">
                        <x-picture :photo="$photo" sizes="(min-width: 1024px) 25vw, (min-width: 768px) 33vw, 50vw" class="block size-full"
                                   imgClass="size-full object-cover transition-[scale] duration-700 ease-glide group-hover:scale-[1.03]" />
                    </button>
                @endforeach
            </div>

            <div class="mt-12 flex justify-center">
                <button type="button" id="archiveMore" data-page="{{ $pageSize }}" class="{{ $pillLight }}" @if ($archive->count() <= $pageSize) hidden @endif>
                    Ver más fotos
                </button>
            </div>
        </div>
    </section>

    <!-- 10 · CIERRE: sobre mí -->
    <section id="sobre-mi" data-nav="light" class="bg-paper pb-24 text-ink md:pb-38">
        <div class="{{ $wrap }} grid items-end gap-y-12 border-t border-rule-light pt-24 md:grid-cols-12 md:gap-x-8 md:pt-38">
            <div class="overflow-hidden md:col-span-5" data-reveal>
                <img src="/img/profile/foto_mark.jpeg" alt="Marco, fotógrafo" width="640" height="641"
                     loading="lazy" decoding="async" class="block aspect-square size-full bg-rule-light object-cover">
            </div>
            <div class="md:col-span-6 md:col-start-7" data-reveal=".1">
                <x-section-label>Sobre mí</x-section-label>
                <p class="mt-10 text-statement font-light">Ingeniero civil boliviano que no para de viajar.</p>
                <p class="mt-8 max-w-[48ch] text-base leading-[1.6] text-gray-light">
                    MarKual es boliviano, ingeniero civil de profesión y viajero de vocación. Entre volcanes, selvas y altiplanos, captura los paisajes que la mayoría no llega a ver — a pie, en bote, en helicóptero o caminando sin rumbo fijo. Amante de las montañas y de los caminos que ni sabía que existían.
                </p>
                <a href="#contacto" class="{{ $pillLight }} mt-10">Trabajemos juntos</a>
            </div>
        </div>
    </section>

    <!-- 10 · CIERRE: contacto sobre el salar -->
    @php $cta = $p->photo($story['cta']); @endphp
    <section id="contacto" data-band data-nav="dark" class="relative overflow-hidden bg-ink text-paper">
        <x-picture :photo="$cta" sizes="100vw" class="absolute inset-x-0 -top-[15%] block h-[130%]" imgClass="size-full object-cover" />
        <div class="absolute inset-0 bg-ink/70"></div>
        <div class="{{ $wrap }} relative py-24 md:py-38">
            <x-section-label tone="dark">Contacto</x-section-label>
            <h2 class="mt-10 max-w-[14ch] text-[clamp(2.75rem,7vw,7.5rem)] leading-[.95] font-light tracking-[-.035em]" data-reveal>
                Sigue el trabajo en tiempo real.
            </h2>
            <div class="mt-16 grid gap-y-12 md:mt-24 md:grid-cols-12 md:gap-x-8">
                <div class="md:col-span-4" data-reveal>
                    <p class="max-w-[38ch] text-base leading-[1.6] text-paper/80">
                        Expediciones en curso, detrás de cámara y el archivo completo.
                        El mejor lugar para colaborar o hacer una consulta es por mensaje directo.
                    </p>
                    <p class="mt-6 text-base">@markualko</p>
                </div>
                <ul class="md:col-span-7 md:col-start-6" data-reveal=".1">
                    @foreach ([
                        ['https://www.instagram.com/markualko', 'Instagram'],
                        ['https://www.threads.net/@markualko', 'Threads'],
                        ['https://wa.me/XXXXXXXXXXX', 'WhatsApp'],
                    ] as [$href, $name])
                        <li class="border-t border-paper/20 last:border-b">
                            <a href="{{ $href }}" target="_blank" rel="noopener"
                               class="group flex items-baseline justify-between py-6 text-[clamp(1.5rem,3vw,2.5rem)] font-light transition-opacity duration-200 ease-out hover:opacity-70">
                                {{ $name }}
                                <span class="text-xl transition-transform duration-200 ease-out group-hover:translate-x-1 group-hover:-translate-y-1" aria-hidden="true">↗</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    </main>

    <!-- FOOTER -->
    <footer data-nav="dark" class="bg-ink text-gray-dark">
        <div class="{{ $wrap }} grid gap-2 border-t border-rule-dark py-10 text-caption md:grid-cols-3">
            <span>© {{ date('Y') }} MarKual</span>
            <span class="md:text-center">Bolivia · Argentina · Chile</span>
            <span class="md:text-right">@markualko</span>
        </div>
    </footer>

    <!-- WHATSAPP FLOTANTE — reemplaza XXXXXXXXXXX con el número completo sin + ni espacios -->
    <a href="https://wa.me/XXXXXXXXXXX" target="_blank" rel="noopener" aria-label="Escribir por WhatsApp" data-wa
       class="fixed right-4 bottom-4 z-[500] flex size-14 items-center justify-center rounded-full border border-paper/25 bg-ink text-paper transition-[background-color,color,scale,opacity,translate] duration-300 ease-out hover:bg-paper hover:text-ink active:scale-[.97] md:right-8 md:bottom-8">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>

    </div>{{-- /#page --}}

    <!-- LIGHTBOX del archivo -->
    <div id="lightbox" role="dialog" aria-modal="true" aria-label="Foto ampliada"
         class="fixed inset-0 z-[2000] hidden flex-col bg-ink text-paper [&.open]:flex">
        <div class="flex items-center justify-between px-4 py-4 md:px-8 md:py-6">
            <span></span>
            <button type="button" id="lbClose"
                    class="rounded-full border border-paper/30 px-5 py-2 text-caption transition-[border-color,background-color,color] duration-150 ease-out hover:border-paper hover:bg-paper hover:text-ink">Cerrar</button>
        </div>
        <div class="flex min-h-0 flex-1 items-center justify-center px-4 md:px-8">
            <img id="lbImg" src="" alt="" class="max-h-full max-w-full animate-lb-in object-contain">
        </div>
        <div class="flex items-center justify-between gap-4 px-4 py-4 md:px-8 md:py-6">
            <p id="lbCaption" class="text-base font-light"></p>
            <div class="flex gap-3">
                <button type="button" id="lbPrev" aria-label="Foto anterior"
                        class="rounded-full border border-paper/30 px-6 py-2 text-base transition-[border-color,background-color,color,scale] duration-150 ease-out hover:border-paper hover:bg-paper hover:text-ink active:scale-[.97]">&#8592;</button>
                <button type="button" id="lbNext" aria-label="Foto siguiente"
                        class="rounded-full border border-paper/30 px-6 py-2 text-base transition-[border-color,background-color,color,scale] duration-150 ease-out hover:border-paper hover:bg-paper hover:text-ink active:scale-[.97]">&#8594;</button>
            </div>
        </div>
    </div>

    <script>
    const page = document.getElementById('page');

    // ── NAV: tinta según la banda que pasa por debajo ──
    const nav = document.getElementById('mainNav');
    const bands = [...document.querySelectorAll('[data-nav]')];
    let navTicking = false;
    function updateNav(){
        navTicking = false;
        const y = nav.offsetHeight / 2;
        const band = bands.find(b => { const r = b.getBoundingClientRect(); return r.top <= y && r.bottom > y; });
        if (band) nav.dataset.theme = band.dataset.nav;
    }
    addEventListener('scroll', () => { if (!navTicking) { navTicking = true; requestAnimationFrame(updateNav); } }, { passive: true });
    addEventListener('resize', updateNav, { passive: true });
    updateNav();

    // ── ARCHIVO: filtros y "ver más" ──
    const items = [...document.querySelectorAll('[data-archive-item]')];
    const filters = document.querySelectorAll('[data-archive-filter]');
    const more = document.getElementById('archiveMore');
    const PAGE = parseInt(more.dataset.page, 10);
    let current = 'all', shown = PAGE;

    function renderArchive(){
        const matching = items.filter(el => current === 'all' || el.dataset.cat === current);
        items.forEach(el => el.hidden = true);
        matching.slice(0, shown).forEach(el => el.hidden = false);
        more.hidden = matching.length <= shown;
        // el alto de la página cambió: los disparadores de scroll que vienen después se recalculan
        window.dispatchEvent(new Event('archive:resize'));
    }
    filters.forEach(btn => btn.addEventListener('click', () => {
        filters.forEach(b => b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'));
        current = btn.dataset.archiveFilter; shown = PAGE;
        renderArchive();
    }));
    more.addEventListener('click', () => { shown += PAGE; renderArchive(); });

    // ── LIGHTBOX ──
    const lightbox = document.getElementById('lightbox');
    const lbImg = document.getElementById('lbImg');
    const lbCaption = document.getElementById('lbCaption');
    const visible = () => items.filter(el => !el.hidden);
    let lbIdx = 0, lastFocus = null;

    function showLb(i){
        const list = visible(); if (!list.length) return;
        lbIdx = (i + list.length) % list.length;
        const el = list[lbIdx];
        lbImg.src = el.dataset.full;
        lbImg.alt = el.dataset.alt;
        lbImg.style.animation = 'none'; void lbImg.getBoundingClientRect(); lbImg.style.animation = '';
        lbCaption.textContent = el.dataset.caption;
    }
    function openLb(el){
        lastFocus = document.activeElement;
        showLb(visible().indexOf(el));
        lightbox.classList.add('open');
        page.inert = true;
        document.body.style.overflow = 'hidden';
        window.lenis?.stop();
        document.getElementById('lbClose').focus();
    }
    function closeLb(){
        if (!lightbox.classList.contains('open')) return;
        lightbox.classList.remove('open');
        page.inert = false;
        document.body.style.overflow = '';
        window.lenis?.start();
        lastFocus?.focus();
    }
    items.forEach(el => el.addEventListener('click', () => openLb(el)));
    document.getElementById('lbClose').addEventListener('click', closeLb);
    document.getElementById('lbPrev').addEventListener('click', () => showLb(lbIdx - 1));
    document.getElementById('lbNext').addEventListener('click', () => showLb(lbIdx + 1));
    document.addEventListener('keydown', e => {
        if (!lightbox.classList.contains('open')) return;
        if (e.key === 'Escape') closeLb();
        if (e.key === 'ArrowRight') showLb(lbIdx + 1);
        if (e.key === 'ArrowLeft') showLb(lbIdx - 1);
    });
    </script>

</body>
</html>
