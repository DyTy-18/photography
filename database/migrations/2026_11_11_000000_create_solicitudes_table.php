<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 16);          // reservar | cotizar
            $table->string('ruta', 64);          // slug de config/rutas.php o "a-medida"
            $table->date('fecha')->nullable();
            $table->string('personas', 8)->nullable();
            $table->string('nombre', 120);
            $table->text('mensaje')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('referrer', 512)->nullable();
            $table->string('landing', 512)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
