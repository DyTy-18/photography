<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Origen del visitante (UTM, referrer, página de llegada): se guarda en la sesión al llegar
 * (o al volver desde otra campaña) y acompaña a la solicitud si termina escribiendo.
 */
class GuardarOrigen
{
    public function handle(Request $request, Closure $next)
    {
        $utm = config('contacto.utm');

        if (! $request->session()->has('origen') || $request->hasAny($utm)) {
            $request->session()->put('origen', array_filter([
                ...$request->only($utm),
                'referrer' => mb_substr((string) $request->headers->get('referer'), 0, 512) ?: null,
                'landing' => mb_substr($request->fullUrl(), 0, 512),
            ]));
        }

        return $next($request);
    }
}
