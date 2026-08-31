<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperadorTuristico;
use App\Models\Ruta;
use App\Models\RutaOperador;
use App\Models\RutaOperadorBloqueo;
use App\Models\RutaOperadorHorario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperadorRutaController extends Controller
{
    public function index(OperadorTuristico $operador)
    {
        $operador->load('rutas');
        $rutasDisponibles = Ruta::where('publicado', true)
            ->whereNotIn('id', $operador->rutas->pluck('id'))
            ->orderBy('titulo')
            ->get();

        return view('admin.operadores.rutas.index', compact('operador', 'rutasDisponibles'));
    }

    public function store(Request $request, OperadorTuristico $operador)
    {
        $request->validate([
            'ruta_id' => 'required|exists:rutas,id',
        ]);

        $operador->rutas()->syncWithoutDetaching([$request->integer('ruta_id')]);

        $rutaOperador = RutaOperador::where('operador_turistico_id', $operador->id)
            ->where('ruta_id', $request->integer('ruta_id'))
            ->firstOrFail();

        return redirect()->route('admin.operadores.rutas.edit', [$operador, $rutaOperador])
            ->with('success', 'Ruta asignada. Ahora configura precios y horarios.');
    }

    public function edit(OperadorTuristico $operador, RutaOperador $rutaOperador)
    {
        abort_unless($rutaOperador->operador_turistico_id === $operador->id, 404);

        $rutaOperador->load('ruta', 'horarios', 'bloqueos');

        return view('admin.operadores.rutas.edit', compact('operador', 'rutaOperador'));
    }

    public function update(Request $request, OperadorTuristico $operador, RutaOperador $rutaOperador)
    {
        abort_unless($rutaOperador->operador_turistico_id === $operador->id, 404);

        $data = $request->validate([
            'ticketing_activo'      => 'nullable|boolean',
            'precio_individual'     => 'required|integer|min:0',
            'precio_grupo_adulto'   => 'required|integer|min:0',
            'precio_nino'           => 'required|integer|min:0',
            'edad_maxima_nino'      => 'required|integer|min:0|max:99',
            'notas_operador'        => 'nullable|string|max:2000',
            'horarios'              => 'nullable|array',
            'horarios.*.id'         => 'nullable|integer',
            'horarios.*.tipo'       => 'required_with:horarios|in:semanal,fecha',
            'horarios.*.dias_semana' => 'nullable|array',
            'horarios.*.dias_semana.*' => 'integer|min:1|max:7',
            'horarios.*.fecha'      => 'nullable|date',
            'horarios.*.hora'       => 'required_with:horarios|date_format:H:i',
            'horarios.*.cupo_maximo' => 'required_with:horarios|integer|min:1',
            'bloqueos'              => 'nullable|array',
            'bloqueos.*.fecha'      => 'required_with:bloqueos|date',
            'bloqueos.*.motivo'     => 'nullable|string|max:255',
        ]);

        $data['ticketing_activo'] = $request->boolean('ticketing_activo');

        DB::transaction(function () use ($data, $rutaOperador) {
            $rutaOperador->update([
                'ticketing_activo'    => $data['ticketing_activo'],
                'precio_individual'   => $data['precio_individual'],
                'precio_grupo_adulto' => $data['precio_grupo_adulto'],
                'precio_nino'         => $data['precio_nino'],
                'edad_maxima_nino'    => $data['edad_maxima_nino'],
                'notas_operador'      => $data['notas_operador'] ?? null,
            ]);

            $idsEnviados = [];

            foreach ($data['horarios'] ?? [] as $fila) {
                $payload = [
                    'ruta_operador_turistico_id' => $rutaOperador->id,
                    'tipo'        => $fila['tipo'],
                    'dias_semana' => $fila['tipo'] === 'semanal'
                        ? array_map('intval', $fila['dias_semana'] ?? [])
                        : null,
                    'fecha'       => $fila['tipo'] === 'fecha' ? $fila['fecha'] : null,
                    'hora'        => $fila['hora'],
                    'cupo_maximo' => $fila['cupo_maximo'],
                    'activo'      => true,
                ];

                if (!empty($fila['id'])) {
                    $horario = RutaOperadorHorario::where('id', $fila['id'])
                        ->where('ruta_operador_turistico_id', $rutaOperador->id)
                        ->first();
                    if ($horario) {
                        $horario->update($payload);
                        $idsEnviados[] = $horario->id;
                        continue;
                    }
                }

                $nuevo = RutaOperadorHorario::create($payload);
                $idsEnviados[] = $nuevo->id;
            }

            $rutaOperador->horarios()
                ->whereNotIn('id', $idsEnviados)
                ->get()
                ->each(function (RutaOperadorHorario $horario) {
                    if ($horario->reservas()->count() > 0) {
                        $horario->update(['activo' => false]);
                    } else {
                        $horario->delete();
                    }
                });

            $fechasBloqueadasEnviadas = [];

            foreach ($data['bloqueos'] ?? [] as $fila) {
                RutaOperadorBloqueo::updateOrCreate(
                    ['ruta_operador_turistico_id' => $rutaOperador->id, 'fecha' => $fila['fecha']],
                    ['motivo' => $fila['motivo'] ?? null]
                );
                $fechasBloqueadasEnviadas[] = $fila['fecha'];
            }

            $rutaOperador->bloqueos()->whereNotIn('fecha', $fechasBloqueadasEnviadas)->delete();
        });

        return redirect()->route('admin.operadores.rutas.edit', [$operador, $rutaOperador])
            ->with('success', 'Configuración de ticketera actualizada.');
    }

    public function destroy(OperadorTuristico $operador, RutaOperador $rutaOperador)
    {
        abort_unless($rutaOperador->operador_turistico_id === $operador->id, 404);

        if ($rutaOperador->reservas()->count() > 0) {
            $rutaOperador->update(['ticketing_activo' => false]);
            return back()->with('error', 'Esta ruta ya tiene reservas asociadas: se desactivó la ticketera pero no se puede desvincular.');
        }

        $rutaOperador->delete();

        return redirect()->route('admin.operadores.rutas.index', $operador)->with('success', 'Ruta desvinculada del operador.');
    }
}
