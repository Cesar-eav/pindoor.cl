<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadContacto extends Model
{
    protected $table = 'leads_contacto';

    const ESTADOS = [
        'pendiente'   => ['label' => 'Pendiente',            'color' => 'amber'],
        'contactado'  => ['label' => 'Contactado',           'color' => 'blue'],
        'interesado'  => ['label' => 'Interesado',           'color' => 'violet'],
        'no_responde' => ['label' => 'No responde',          'color' => 'gray'],
        'descartado'  => ['label' => 'Descartado',           'color' => 'red'],
        'convertido'  => ['label' => 'Convertido en cliente','color' => 'green'],
    ];

    protected $fillable = [
        'tipo',
        'nombre',
        'email',
        'telefono',
        'tipo_negocio',
        'nombre_negocio',
        'especialidad',
        'ciudad',
        'mensaje',
        'estado',
        'observaciones',
    ];

    public function estadoInfo(): array
    {
        return self::ESTADOS[$this->estado] ?? self::ESTADOS['pendiente'];
    }
}
