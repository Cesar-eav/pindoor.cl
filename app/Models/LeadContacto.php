<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadContacto extends Model
{
    protected $table = 'leads_contacto';

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
        'contactado',
    ];
}
