<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SolicitudController extends Controller
{
    /**
     * Guarda el lead (con su origen/UTM) y devuelve el enlace de WhatsApp con el mensaje ya
     * escrito. La conversación sigue ahí: no se manda ningún correo.
     */
    public function store(Request $request)
    {
        // Honeypot: un humano no ve este campo. Se responde como si todo hubiera ido bien.
        if (filled($request->input('website'))) {
            return $this->respond($request, null);
        }

        $data = $request->validate([
            'tipo' => ['required', Rule::in(['reservar', 'cotizar'])],
            'ruta' => ['required', Rule::in([...array_keys(config('rutas')), 'a-medida'])],
            'fecha' => ['nullable', 'date', 'after_or_equal:today'],
            'personas' => ['nullable', Rule::in([...array_map('strval', range(1, 10)), '10+'])],
            'nombre' => ['required', 'string', 'max:120'],
            'mensaje' => ['nullable', 'string', 'max:2000'],
        ], [
            'tipo.*' => 'Elige si quieres reservar o solo cotizar.',
            'ruta.*' => 'Elige una ruta.',
            'fecha.after_or_equal' => 'La fecha tiene que ser de hoy en adelante.',
            'fecha.*' => 'Revisa la fecha.',
            'nombre.required' => 'Escribe tu nombre.',
        ]);

        $origen = $request->session()->get('origen', []);
        $solicitud = Solicitud::create($data + array_intersect_key($origen, array_flip([
            ...config('contacto.utm'), 'referrer', 'landing',
        ])));

        return $this->respond($request, $solicitud);
    }

    private function respond(Request $request, ?Solicitud $solicitud)
    {
        $whatsapp = $solicitud ? $this->whatsappUrl($solicitud) : null;

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'whatsapp' => $whatsapp]);
        }

        // Sin JS: directo a WhatsApp
        return $whatsapp
            ? redirect()->away($whatsapp)
            : redirect()->to(url('/').'#experimenta')->with('solicitud_enviada', true);
    }

    /** Enlace a WhatsApp con todos los datos de la solicitud ya escritos. */
    private function whatsappUrl(Solicitud $s): ?string
    {
        $numero = preg_replace('/\D/', '', (string) config('contacto.whatsapp'));
        if ($numero === '') {
            return null;
        }

        $ruta = config("rutas.{$s->ruta}.titulo");
        $lineas = array_filter([
            sprintf(
                'Hola Marco, soy %s. Quiero %s %s.',
                $s->nombre,
                $s->tipo === 'reservar' ? 'reservar' : 'cotizar',
                $ruta ? "la ruta {$ruta}" : 'una ruta a medida',
            ),
            $s->fecha ? 'Fecha aproximada: '.$s->fecha->format('d/m/Y') : null,
            $s->personas ? 'Personas: '.($s->personas === '10+' ? 'más de 10' : $s->personas) : null,
            $s->mensaje,
        ]);

        return 'https://wa.me/'.$numero.'?text='.rawurlencode(implode("\n", $lineas));
    }
}
