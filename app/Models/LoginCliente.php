<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginCliente extends Model
{
    protected $table = 'login_clientes';

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
