    {{--
        Rutas: tarjetas + popup (carrusel y formulario). Lo usan la portada (#experimenta) y la
        página /rutas. Variables: $rutas, $p; opcionales $standalone (página propia: lleva el h1)
        y $abrir (slug cuyo popup se abre al cargar, para los enlaces /rutas/{slug}).
    --}}
    @php
        $standalone ??= false;
        $abrir ??= null;
        $wrap ??= 'mx-auto w-full max-w-[1280px] px-4 md:px-10';
        $hTarjeta = $standalone ? 'h2' : 'h3';
        $field = 'mt-2 block w-full rounded-none border-0 border-b border-paper/30 bg-transparent px-0 py-3 text-lg font-light text-paper [color-scheme:dark] transition-[border-color] duration-200 ease-out placeholder:text-gray-dark focus:border-paper focus:outline-none aria-invalid:border-[#ff9b8a]';
        $label = 'block text-caption text-gray-dark';
        $err = 'mt-2 text-caption text-[#ff9b8a]';
        $rutaSel = old('ruta', request('ruta'));
        $btnDark = 'rounded-full border border-paper/30 px-5 py-2 text-caption transition-[border-color,background-color,color] duration-150 ease-out hover:border-paper hover:bg-paper hover:text-ink';

        // Galerías del popup: portada + fotos de la ruta. "A medida" junta las portadas de todas.
        $galerias = collect($rutas)->map(fn ($r) => [
            'titulo' => $r['titulo'], 'zona' => $r['zona'], 'resumen' => $r['resumen'],
            'meta' => "{$r['duracion']} · {$r['nivel']}",
            'fotos' => $p->many([$r['foto'], ...($r['fotos'] ?? [])]),
        ])->put('a-medida', [
            'titulo' => 'Ruta a medida', 'zona' => 'Bolivia', 'meta' => 'Tú eliges',
            'resumen' => 'Dime qué lugares te interesan y cuántos días tienes, y armamos el viaje juntos.',
            'fotos' => $p->many(array_column($rutas, 'foto')),
        ]);
    @endphp
    <section id="experimenta" data-nav="light" class="bg-paper pb-24 text-ink md:pb-38">
        <div @class([$wrap, 'pt-36 md:pt-48' => $standalone, 'border-t border-rule-light pt-24 md:pt-38' => ! $standalone])>
            @if ($standalone)
                <p class="text-caption">Rutas</p>
            @else
                <x-section-label>Rutas</x-section-label>
            @endif
            <div class="mt-10 grid gap-y-8 md:grid-cols-12 md:gap-x-8">
                @if ($standalone)
                    <h1 class="max-w-[14ch] text-statement font-light md:col-span-7" data-reveal>Experimenta Bolivia conmigo.</h1>
                @else
                    <p class="max-w-[14ch] text-statement font-light md:col-span-7" data-reveal>Experimenta Bolivia conmigo.</p>
                @endif
                <p class="max-w-[42ch] text-base leading-[1.6] text-gray-light md:col-span-4 md:col-start-9 md:self-end" data-reveal=".1">
                    Son lugares que conozco bien y a los que vuelvo cada año. Elige uno, dime cuándo
                    y cuántos van, y te mando una propuesta. Pedirla no te compromete a nada.
                </p>
            </div>

            <ul class="mt-16 grid gap-x-8 gap-y-16 md:mt-24 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($rutas as $slug => $ruta)
                    @php $foto = $p->photo($ruta['foto']); @endphp
                    {{-- Toda la tarjeta es clicable: el botón se estira sobre ella con after:inset-0 --}}
                    <li class="group relative flex flex-col" data-reveal="{{ ($loop->index % 3) * .08 }}">
                        <div class="aspect-[4/3] overflow-hidden">
                            @if ($foto)
                                <x-picture :photo="$foto" sizes="(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw" class="block size-full"
                                           imgClass="size-full object-cover transition-[scale] duration-700 ease-glide group-hover:scale-[1.03]" />
                            @else
                                <div class="size-full bg-rule-light"></div>
                            @endif
                        </div>
                        <p class="mt-6 text-caption text-gray-light">{{ $ruta['duracion'] }} · {{ $ruta['nivel'] }}</p>
                        <{{ $hTarjeta }} class="mt-3 text-[clamp(1.5rem,2.4vw,2rem)] leading-[1.1] font-light tracking-[-.02em]">{{ $ruta['titulo'] }}</{{ $hTarjeta }}>
                        <p class="mt-1 text-caption text-gray-light">{{ $ruta['zona'] }}</p>
                        <p class="mt-4 max-w-[40ch] text-base leading-[1.6] text-gray-light">{{ $ruta['resumen'] }}</p>
                        <button type="button" data-ruta="{{ $slug }}" aria-haspopup="dialog"
                                class="mt-6 inline-flex items-baseline gap-2 self-start border-b border-ink pb-1 text-base transition-opacity duration-200 ease-out after:absolute after:inset-0 hover:opacity-60">
                            Ver fotos y reservar <span class="sr-only">{{ $ruta['titulo'] }}</span><span aria-hidden="true" class="inline-block transition-transform duration-200 ease-out group-hover:translate-x-1">→</span>
                        </button>
                    </li>
                @endforeach
            </ul>

            <p class="mt-20 text-base text-gray-light" data-reveal>
                ¿Tienes otro lugar en mente?
                <button type="button" data-ruta="a-medida" aria-haspopup="dialog" class="border-b border-ink pb-0.5 text-ink transition-opacity duration-200 ease-out hover:opacity-60">Armamos una ruta a medida</button>.
            </p>
        </div>

        {{-- Fotos de cada popup: en <template> para que no se descarguen hasta abrirlo --}}
        @foreach ($galerias as $slug => $g)
            <template data-ruta-tpl="{{ $slug }}" data-titulo="{{ $g['titulo'] }}" data-zona="{{ $g['zona'] }}"
                      data-meta="{{ $g['meta'] }}" data-resumen="{{ $g['resumen'] }}">
                @foreach ($g['fotos'] as $foto)
                    <x-picture :photo="$foto" sizes="(min-width: 768px) 560px, 100vw"
                               class="block size-full shrink-0 snap-center snap-always" imgClass="size-full object-cover" />
                @endforeach
            </template>
        @endforeach
    </section>

    <!-- POPUP DE RUTA: carrusel de fotos + formulario de reserva (lead → WhatsApp) -->
    @php $navBtn = 'flex size-10 items-center justify-center rounded-full border border-paper/30 bg-ink/60 text-base backdrop-blur-sm transition-[background-color,color,border-color,scale] duration-150 ease-out hover:border-paper hover:bg-paper hover:text-ink active:scale-[.97] disabled:pointer-events-none disabled:opacity-40'; @endphp
    {{-- data-share: base de los enlaces /rutas/{slug}. data-sync-url: en /rutas la barra de direcciones sigue al popup. --}}
    <dialog id="rutaModal" aria-labelledby="rmTitulo" data-share="{{ url('/rutas') }}"
            @if ($standalone) data-sync-url @endif @if ($abrir) data-open="{{ $abrir }}" @endif
            class="m-auto flex max-h-[92dvh] w-[calc(100%-2rem)] max-w-[1040px] flex-col overflow-hidden bg-ink p-0 text-paper not-open:hidden backdrop:bg-ink/75 backdrop:backdrop-blur-sm md:grid md:h-[min(88dvh,680px)] md:grid-cols-[1.1fr_1fr]">
        <div class="absolute top-3 right-3 z-10 flex gap-2">
            <button type="button" id="rmShare" class="{{ $btnDark }} bg-ink/70 backdrop-blur-sm"><span data-share-label>Compartir</span></button>
            <button type="button" id="rmClose" class="{{ $btnDark }} bg-ink/70 backdrop-blur-sm">Cerrar</button>
        </div>

        {{-- Carrusel: scroll-snap nativo (se desliza con el dedo); flechas y contador desde reserva.js --}}
        <div class="relative aspect-[4/3] shrink-0 bg-ink-soft md:aspect-auto md:h-full md:min-h-0">
            <div id="rmFotos" tabindex="0" role="group" aria-roledescription="carrusel" aria-label="Fotos de la ruta"
                 class="flex size-full snap-x snap-mandatory overflow-x-auto overscroll-x-contain [scrollbar-width:none] focus-visible:outline-offset-[-4px] [&::-webkit-scrollbar]:hidden"></div>
            <p id="rmCount" aria-live="polite" class="absolute bottom-3 left-3 rounded-full bg-ink/60 px-3 py-1.5 text-caption tabular-nums backdrop-blur-sm"></p>
            <div class="absolute right-3 bottom-3 flex gap-2">
                <button type="button" id="rmPrev" aria-label="Foto anterior" class="{{ $navBtn }}">&#8592;</button>
                <button type="button" id="rmNext" aria-label="Foto siguiente" class="{{ $navBtn }}">&#8594;</button>
            </div>
        </div>

        {{-- data-lenis-prevent: Lenis está parado mientras el popup está abierto y si no bloquearía este scroll --}}
        <div id="rmPanel" data-lenis-prevent class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-5 pt-6 pb-8 md:px-8 md:pt-14">
            <p id="rmMeta" class="text-caption text-gray-dark"></p>
            <h2 id="rmTitulo" class="mt-2 text-[clamp(1.75rem,3vw,2.5rem)] leading-[1.05] font-light tracking-[-.02em]"></h2>
            <p id="rmZona" class="mt-2 text-caption text-gray-dark"></p>
            <p id="rmResumen" class="mt-4 text-base leading-[1.6] text-paper/80"></p>

                    <div class="mt-8 border-t border-paper/20 pt-8">
                        <div id="reservaOk" tabindex="-1" hidden class="animate-lb-in focus:outline-none">
                            <p class="text-[clamp(1.75rem,3vw,2.5rem)] leading-[1.05] font-light">Ya casi.</p>
                            <p class="mt-6 max-w-[42ch] text-base leading-[1.6] text-paper/80">
                                Tu mensaje está listo en WhatsApp. Si no se abrió solo, toca el botón y envíalo desde ahí.
                            </p>
                            <a id="reservaWa" href="https://wa.me/{{ config('contacto.whatsapp') }}" target="_blank" rel="noopener"
                               class="mt-10 inline-flex items-center gap-2 rounded-full border border-paper px-[33px] py-[11px] text-base transition-[background-color,color,scale] duration-200 ease-out hover:bg-paper hover:text-ink active:scale-[.97]">
                                Abrir WhatsApp <span aria-hidden="true">↗</span>
                            </a>
                        </div>

                        <form id="reservaForm" action="{{ route('solicitudes.store') }}" method="POST" novalidate
                              class="grid gap-x-6 gap-y-8 sm:grid-cols-2">
                            @csrf
                            <p class="text-base leading-[1.6] text-paper/80 sm:col-span-2">
                                Completa los datos y se abre WhatsApp con el mensaje ya escrito. Solo tienes que enviarlo.
                            </p>
                            {{-- Honeypot: invisible para personas --}}
                            <div class="absolute -left-[9999px]" aria-hidden="true">
                                <label>No llenes este campo <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                            </div>

                            <fieldset class="sm:col-span-2">
                                <legend class="{{ $label }}">¿Qué necesitas?</legend>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach (['reservar' => 'Quiero reservar', 'cotizar' => 'Solo cotizar'] as $value => $text)
                                        <label class="cursor-pointer rounded-full border border-paper/30 px-5 py-2.5 text-base transition-[background-color,color,border-color,scale] duration-200 ease-out hover:border-paper active:scale-[.97] has-checked:border-paper has-checked:bg-paper has-checked:text-ink has-focus-visible:outline-2 has-focus-visible:outline-offset-4 has-focus-visible:outline-paper">
                                            <input type="radio" name="tipo" value="{{ $value }}" class="sr-only" @checked(old('tipo', 'cotizar') === $value)>
                                            {{ $text }}
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>

                            <div class="sm:col-span-2">
                                <label for="f-ruta" class="{{ $label }}">Ruta</label>
                                <select id="f-ruta" name="ruta" required class="{{ $field }} [&_option]:bg-ink"
                                        aria-describedby="e-ruta" @error('ruta') aria-invalid="true" @enderror>
                                    <option value="" disabled @selected(! $rutaSel)>Elige una ruta</option>
                                    @foreach ($rutas as $slug => $ruta)
                                        <option value="{{ $slug }}" @selected($rutaSel === $slug)>{{ $ruta['titulo'] }} · {{ $ruta['duracion'] }}</option>
                                    @endforeach
                                    <option value="a-medida" @selected($rutaSel === 'a-medida')>Una ruta a medida</option>
                                </select>
                                <p id="e-ruta" data-error="ruta" class="{{ $err }}" @unless ($errors->has('ruta')) hidden @endunless>{{ $errors->first('ruta') }}</p>
                            </div>

                            <div>
                                <label for="f-fecha" class="{{ $label }}">Fecha aproximada (opcional)</label>
                                <input id="f-fecha" type="date" name="fecha" min="{{ now()->toDateString() }}" value="{{ old('fecha') }}" class="{{ $field }}"
                                       aria-describedby="e-fecha" @error('fecha') aria-invalid="true" @enderror>
                                <p id="e-fecha" data-error="fecha" class="{{ $err }}" @unless ($errors->has('fecha')) hidden @endunless>{{ $errors->first('fecha') }}</p>
                            </div>

                            <div>
                                <label for="f-personas" class="{{ $label }}">Personas</label>
                                <select id="f-personas" name="personas" class="{{ $field }} [&_option]:bg-ink">
                                    @foreach ([...array_map('strval', range(1, 10)), '10+'] as $n)
                                        <option value="{{ $n }}" @selected(old('personas', '2') === $n)>{{ $n === '10+' ? 'Más de 10' : $n }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="f-nombre" class="{{ $label }}">Nombre</label>
                                <input id="f-nombre" type="text" name="nombre" required autocomplete="name" maxlength="120" value="{{ old('nombre') }}" class="{{ $field }}"
                                       aria-describedby="e-nombre" @error('nombre') aria-invalid="true" @enderror>
                                <p id="e-nombre" data-error="nombre" class="{{ $err }}" @unless ($errors->has('nombre')) hidden @endunless>{{ $errors->first('nombre') }}</p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="f-mensaje" class="{{ $label }}">Algo más que deba saber (opcional)</label>
                                <textarea id="f-mensaje" name="mensaje" rows="3" maxlength="2000" class="{{ $field }} resize-y"
                                          placeholder="Experiencia en montaña, si viajas con niños, si tus fechas son flexibles…">{{ old('mensaje') }}</textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-full border border-paper bg-paper px-[33px] py-[11px] text-base text-ink transition-[background-color,color,scale,opacity] duration-200 ease-out hover:bg-transparent hover:text-paper active:scale-[.97] disabled:pointer-events-none disabled:opacity-60">
                                    Continuar en WhatsApp
                                </button>
                            </div>
                            <p id="reservaError" role="alert" class="{{ $err }} sm:col-span-2" hidden></p>
                        </form>
                    </div>
        </div>
    </dialog>
