<?php

namespace Tests\Feature;

use App\Models\Solicitud;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitudTest extends TestCase
{
    use RefreshDatabase;

    private function datos(array $extra = []): array
    {
        return $extra + [
            'tipo' => 'reservar',
            'ruta' => 'salar',
            'fecha' => now()->addMonth()->toDateString(),
            'personas' => '2',
            'nombre' => 'Ana',
        ];
    }

    public function test_guarda_la_solicitud_con_los_utm_de_la_visita(): void
    {
        config(['contacto.whatsapp' => '59170000000']);
        $this->get('/?utm_source=instagram&utm_campaign=salar');

        $res = $this->postJson('/solicitudes', $this->datos())->assertOk()->assertJsonPath('ok', true);
        $this->assertStringStartsWith('https://wa.me/59170000000?text=', $res->json('whatsapp'));
        $this->assertStringContainsString('reservar la ruta Salar de Uyuni', urldecode($res->json('whatsapp')));

        $s = Solicitud::sole();
        $this->assertSame('salar', $s->ruta);
        $this->assertSame('instagram', $s->utm_source);
        $this->assertSame('salar', $s->utm_campaign);
    }

    public function test_pide_nombre_y_una_ruta_valida(): void
    {
        $this->postJson('/solicitudes', $this->datos(['ruta' => 'no-existe', 'nombre' => '']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ruta', 'nombre']);

        $this->assertSame(0, Solicitud::count());
    }

    public function test_el_honeypot_no_guarda_nada(): void
    {
        $this->postJson('/solicitudes', $this->datos(['website' => 'spam']))->assertOk();

        $this->assertSame(0, Solicitud::count());
    }

    public function test_sin_js_redirige_directo_a_whatsapp(): void
    {
        config(['contacto.whatsapp' => '59170000000']);

        $res = $this->post('/solicitudes', $this->datos())->assertRedirect();
        $this->assertStringStartsWith('https://wa.me/59170000000?text=', $res->headers->get('Location'));
    }

    public function test_pagina_de_rutas_y_enlace_por_ruta(): void
    {
        $this->get('/rutas')->assertOk()->assertSee('Experimenta Bolivia conmigo.');
        $this->get('/rutas/salar')->assertOk()->assertSee('data-open="salar"', false)->assertSee('<title>Salar de Uyuni', false);
        $this->get('/rutas/no-existe')->assertNotFound();
    }

    public function test_la_utm_de_un_enlace_a_rutas_acompana_a_la_solicitud(): void
    {
        $this->get('/rutas/sajama?utm_source=whatsapp');
        $this->postJson('/solicitudes', $this->datos(['ruta' => 'sajama']))->assertOk();

        $this->assertSame('whatsapp', Solicitud::sole()->utm_source);
    }
}
