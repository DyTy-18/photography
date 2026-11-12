<?php

use App\Http\Controllers\SolicitudController;
use App\Http\Middleware\GuardarOrigen;
use App\Support\Portfolio;
use Illuminate\Support\Facades\Route;

Route::middleware(GuardarOrigen::class)->group(function () {
    Route::get('/', function (Portfolio $portfolio) {
        return view('welcome', [
            'p' => $portfolio,
            'story' => config('story'),
            'archive' => $portfolio->archive(),
            'rutas' => config('rutas'),
        ]);
    });

    // Solo las rutas. /rutas/{slug} abre el popup de esa ruta: es el enlace que se comparte.
    Route::get('/rutas/{ruta?}', function (Portfolio $portfolio, ?string $ruta = null) {
        $rutas = config('rutas');
        abort_if($ruta !== null && $ruta !== 'a-medida' && ! isset($rutas[$ruta]), 404);

        return view('rutas', ['p' => $portfolio, 'rutas' => $rutas, 'abrir' => $ruta]);
    })->name('rutas');
});

Route::post('/solicitudes', [SolicitudController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('solicitudes.store');
