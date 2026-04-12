<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadPublicita extends Model
{
    protected $table = 'leads_publicita';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'negocio',
        'mensaje',
        'contactado',
    ];
}
