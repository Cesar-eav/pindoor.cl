<?php

namespace App\Livewire;

use App\Models\CategoriaEvento;
use App\Models\ModuloItem;
use App\Models\PuntoInteres;
use App\Services\ImagenComprimida;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ClienteEventos extends Component
{
    use WithFileUploads;

    public PuntoInteres $punto;

    public bool $mostrarForm = false;
    public ?int $editandoId = null;

    public string $titulo = '';
    public string $descripcion = '';
    public string $tipo = '';
    public string $fecha = '';
    public string $hora = '';
    public string $hora_fin = '';
    public ?float $precio = null;
    public string $precio_texto = '';
    public string $url_entradas = '';
    public bool $destacado = false;
    public $imagen = null;
    public ?string $imagenActualUrl = null;

    public ?string $mensaje = null;

    public function mount(PuntoInteres $punto): void
    {
        $this->punto = $punto;
    }

    protected function rules(): array
    {
        return [
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'tipo'         => 'required|string|in:' . implode(',', CategoriaEvento::slugs()),
            'fecha'        => 'required|date',
            'hora'         => 'nullable|date_format:H:i',
            'hora_fin'     => 'nullable|date_format:H:i',
            'precio'       => 'nullable|numeric|min:0',
            'precio_texto' => 'nullable|string|max:100',
            'url_entradas' => 'nullable|url|max:255',
            'imagen'       => 'nullable|image|max:20480',
        ];
    }

    public function nuevo(): void
    {
        $this->resetForm();
        $this->mostrarForm = true;
    }

    public function editar(int $id): void
    {
        $evento = ModuloItem::where('id', $id)
            ->where('punto_interes_id', $this->punto->id)
            ->where('modulo', 'eventos')
            ->firstOrFail();

        $this->editandoId      = $evento->id;
        $this->titulo          = $evento->datos['titulo'] ?? '';
        $this->descripcion     = $evento->datos['descripcion'] ?? '';
        $this->tipo            = $evento->datos['tipo'] ?? '';
        $this->fecha           = optional($evento->fecha)->format('Y-m-d') ?? '';
        $this->hora            = $evento->datos['hora'] ?? '';
        $this->hora_fin        = $evento->datos['hora_fin'] ?? '';
        $this->precio          = $evento->datos['precio'] ?? null;
        $this->precio_texto    = $evento->datos['precio_texto'] ?? '';
        $this->url_entradas    = $evento->datos['url_entradas'] ?? '';
        $this->destacado       = (bool) $evento->destacado;
        $this->imagenActualUrl = $evento->imagen ? asset('storage/' . $evento->imagen) : null;
        $this->imagen          = null;
        $this->mostrarForm     = true;
    }

    public function cancelar(): void
    {
        $this->resetForm();
        $this->mostrarForm = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editandoId', 'titulo', 'descripcion', 'tipo', 'fecha', 'hora', 'hora_fin',
            'precio', 'precio_texto', 'url_entradas', 'destacado', 'imagen', 'imagenActualUrl',
        ]);
        $this->resetErrorBag();
    }

    public function guardar(): void
    {
        $this->validate();

        $datos = [
            'titulo'       => $this->titulo,
            'descripcion'  => $this->descripcion,
            'tipo'         => $this->tipo,
            'hora'         => $this->hora ?: null,
            'hora_fin'     => $this->hora_fin ?: null,
            'precio'       => $this->precio,
            'precio_texto' => $this->precio_texto ?: null,
            'url_entradas' => $this->url_entradas ?: null,
        ];

        $rutaImagen = null;
        if ($this->imagen) {
            $rutaImagen = ImagenComprimida::guardar($this->imagen, 'eventos');
        }

        if ($this->editandoId) {
            $item = ModuloItem::where('id', $this->editandoId)
                ->where('punto_interes_id', $this->punto->id)
                ->where('modulo', 'eventos')
                ->firstOrFail();

            if ($rutaImagen) {
                if ($item->imagen) {
                    Storage::disk('public')->delete($item->imagen);
                }
                $item->imagen = $rutaImagen;
            }
            $item->datos     = $datos;
            $item->fecha     = $this->fecha;
            $item->destacado = $this->destacado;
            $item->save();
        } else {
            ModuloItem::create([
                'punto_interes_id' => $this->punto->id,
                'modulo'           => 'eventos',
                'datos'            => $datos,
                'imagen'           => $rutaImagen,
                'activo'           => true,
                'destacado'        => $this->destacado,
                'fecha'            => $this->fecha,
            ]);
        }

        $this->resetForm();
        $this->mostrarForm = false;
        $this->mensaje     = $this->editandoId ? 'Evento actualizado.' : 'Evento guardado en la agenda.';
    }

    public function eliminar(int $id): void
    {
        $evento = ModuloItem::where('id', $id)
            ->where('punto_interes_id', $this->punto->id)
            ->where('modulo', 'eventos')
            ->firstOrFail();

        if ($evento->imagen) {
            Storage::disk('public')->delete($evento->imagen);
        }
        $evento->delete();

        $this->mensaje = 'Evento eliminado.';
    }

    public function render()
    {
        $eventos = ModuloItem::where('punto_interes_id', $this->punto->id)
            ->where('modulo', 'eventos')
            ->get();

        return view('livewire.cliente-eventos', [
            'eventos'     => $eventos,
            'tiposEvento' => CategoriaEvento::catalogo(),
        ]);
    }
}
