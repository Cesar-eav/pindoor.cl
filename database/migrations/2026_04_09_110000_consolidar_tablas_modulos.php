<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Consolida toda la lógica de módulos en dos tablas:
 *
 *  - punto_modulo_datos  → un registro por punto+módulo (datos singleton: carta, menú, alojamiento…)
 *  - punto_modulo_items  → múltiples registros por punto+módulo (listas: entradas, exposiciones, eventos…)
 *
 * Migra los datos desde:
 *  - Columnas individuales en puntosinteres  (menu_del_dia, carta, check_in…)
 *  - Tablas específicas ya existentes        (entradas_museo, exposiciones_museo, eventos_agenda)
 *
 * Al final elimina columnas y tablas obsoletas.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════════════════
        // 1. CREAR TABLAS NUEVAS
        // ═══════════════════════════════════════════════════════════

        Schema::create('punto_modulo_datos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punto_interes_id')->constrained('puntosinteres')->onDelete('cascade');
            $table->string('modulo', 50);           // 'carta', 'menu_del_dia', 'alojamiento'…
            $table->json('datos');                   // estructura libre según el módulo
            $table->timestamp('actualizado_en')->nullable();
            $table->timestamps();

            $table->unique(['punto_interes_id', 'modulo']);
        });

        Schema::create('punto_modulo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punto_interes_id')->constrained('puntosinteres')->onDelete('cascade');
            $table->string('modulo', 50);           // 'entradas', 'exposiciones', 'eventos', 'habitaciones'…
            $table->json('datos');                   // estructura libre según el módulo
            $table->string('imagen')->nullable();    // ruta en storage
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->boolean('destacado')->default(false);
            $table->date('fecha')->nullable();       // para módulos con fecha (eventos)
            $table->timestamps();

            $table->index(['punto_interes_id', 'modulo']);
        });

        // ═══════════════════════════════════════════════════════════
        // 2. MIGRAR DATOS DE COLUMNAS → punto_modulo_datos
        // ═══════════════════════════════════════════════════════════

        // --- Menú del día ---
        DB::table('puntosinteres')
            ->whereNotNull('menu_del_dia')
            ->where('menu_del_dia', '!=', '')
            ->orderBy('id')
            ->each(function ($punto) {
                DB::table('punto_modulo_datos')->insert([
                    'punto_interes_id' => $punto->id,
                    'modulo'           => 'menu_del_dia',
                    'datos'            => json_encode(['texto' => $punto->menu_del_dia]),
                    'actualizado_en'   => $punto->menu_del_dia_updated_at,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            });

        // --- Carta ---
        DB::table('puntosinteres')
            ->where(fn($q) => $q->whereNotNull('carta')->orWhereNotNull('carta_pdf'))
            ->orderBy('id')
            ->each(function ($punto) {
                DB::table('punto_modulo_datos')->insert([
                    'punto_interes_id' => $punto->id,
                    'modulo'           => 'carta',
                    'datos'            => json_encode([
                        'texto'    => $punto->carta,
                        'pdf_ruta' => $punto->carta_pdf,
                    ]),
                    'actualizado_en'   => $punto->carta_updated_at,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            });

        // --- Alojamiento (check_in, check_out, habitaciones, servicios, politicas) ---
        DB::table('puntosinteres')
            ->where(fn($q) => $q
                ->whereNotNull('precio_desde')
                ->orWhereNotNull('check_in')
                ->orWhereNotNull('check_out')
                ->orWhereNotNull('tipos_habitacion')
                ->orWhereNotNull('servicios_incluidos')
                ->orWhereNotNull('politicas')
            )
            ->orderBy('id')
            ->each(function ($punto) {
                DB::table('punto_modulo_datos')->insert([
                    'punto_interes_id' => $punto->id,
                    'modulo'           => 'alojamiento',
                    'datos'            => json_encode([
                        'precio_desde' => $punto->precio_desde,
                        'entrada'      => $punto->check_in,
                        'salida'       => $punto->check_out,
                        'habitaciones' => $punto->tipos_habitacion,
                        'servicios'    => json_decode($punto->servicios_incluidos ?? '[]', true),
                        'politicas'    => $punto->politicas,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        // ═══════════════════════════════════════════════════════════
        // 3. MIGRAR TABLAS ESPECÍFICAS → punto_modulo_items
        // ═══════════════════════════════════════════════════════════

        // --- entradas_museo → 'entradas' ---
        if (Schema::hasTable('entradas_museo')) {
            DB::table('entradas_museo')->orderBy('id')->each(function ($fila) {
                DB::table('punto_modulo_items')->insert([
                    'punto_interes_id' => $fila->punto_interes_id,
                    'modulo'           => 'entradas',
                    'datos'            => json_encode([
                        'etiqueta' => $fila->label,
                        'precio'   => $fila->precio,
                        'nota'     => $fila->descripcion,
                    ]),
                    'activo'      => $fila->activo,
                    'orden'       => $fila->orden,
                    'destacado'   => false,
                    'created_at'  => $fila->created_at,
                    'updated_at'  => $fila->updated_at,
                ]);
            });
        }

        // --- exposiciones_museo → 'exposiciones' ---
        if (Schema::hasTable('exposiciones_museo')) {
            DB::table('exposiciones_museo')->orderBy('id')->each(function ($fila) {
                DB::table('punto_modulo_items')->insert([
                    'punto_interes_id' => $fila->punto_interes_id,
                    'modulo'           => 'exposiciones',
                    'datos'            => json_encode([
                        'titulo'       => $fila->titulo,
                        'descripcion'  => $fila->descripcion,
                        'tipo'         => $fila->tipo,
                        'fecha_inicio' => $fila->fecha_inicio,
                        'fecha_fin'    => $fila->fecha_fin,
                    ]),
                    'imagen'      => $fila->imagen,
                    'activo'      => $fila->activo,
                    'orden'       => $fila->orden,
                    'destacado'   => false,
                    'created_at'  => $fila->created_at,
                    'updated_at'  => $fila->updated_at,
                ]);
            });
        }

        // --- eventos_agenda → 'eventos' ---
        if (Schema::hasTable('eventos_agenda')) {
            DB::table('eventos_agenda')->orderBy('id')->each(function ($fila) {
                DB::table('punto_modulo_items')->insert([
                    'punto_interes_id' => $fila->punto_interes_id,
                    'modulo'           => 'eventos',
                    'datos'            => json_encode([
                        'titulo'       => $fila->titulo,
                        'descripcion'  => $fila->descripcion,
                        'tipo'         => $fila->tipo,
                        'hora'         => $fila->hora,
                        'hora_fin'     => $fila->hora_fin,
                        'precio'       => $fila->precio,
                        'precio_texto' => $fila->precio_descripcion,
                        'url_entradas' => $fila->url_entradas,
                    ]),
                    'imagen'      => $fila->imagen,
                    'activo'      => $fila->activo,
                    'orden'       => 0,
                    'destacado'   => $fila->destacado,
                    'fecha'       => $fila->fecha,
                    'created_at'  => $fila->created_at,
                    'updated_at'  => $fila->updated_at,
                ]);
            });
        }

        // ═══════════════════════════════════════════════════════════
        // 4. ELIMINAR TABLAS ESPECÍFICAS OBSOLETAS
        // ═══════════════════════════════════════════════════════════
        Schema::dropIfExists('entradas_museo');
        Schema::dropIfExists('exposiciones_museo');
        Schema::dropIfExists('eventos_agenda');

        // ═══════════════════════════════════════════════════════════
        // 5. ELIMINAR COLUMNAS OBSOLETAS DE puntosinteres
        // ═══════════════════════════════════════════════════════════
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->dropColumn([
                'menu_del_dia',
                'menu_del_dia_updated_at',
                'carta',
                'carta_pdf',
                'carta_updated_at',
                'precio_desde',
                'check_in',
                'check_out',
                'tipos_habitacion',
                'servicios_incluidos',
                'politicas',
            ]);
        });
    }

    public function down(): void
    {
        // Restaurar columnas
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->text('menu_del_dia')->nullable();
            $table->timestamp('menu_del_dia_updated_at')->nullable();
            $table->text('carta')->nullable();
            $table->string('carta_pdf')->nullable();
            $table->timestamp('carta_updated_at')->nullable();
            $table->string('precio_desde')->nullable();
            $table->string('check_in')->nullable();
            $table->string('check_out')->nullable();
            $table->text('tipos_habitacion')->nullable();
            $table->json('servicios_incluidos')->nullable();
            $table->text('politicas')->nullable();
        });

        Schema::dropIfExists('punto_modulo_items');
        Schema::dropIfExists('punto_modulo_datos');
    }
};
