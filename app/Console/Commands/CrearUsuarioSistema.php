<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CrearUsuarioSistema extends Command
{
    protected $signature   = 'usuario:crear-sistema';
    protected $description = 'Crea el usuario placeholder que es dueño de los perfiles básicos (no reclamados)';

    public function handle(): int
    {
        $existente = User::where('es_sistema', true)->first();

        if ($existente) {
            $this->info("El usuario sistema ya existe: {$existente->name} <{$existente->email}>");
            return self::SUCCESS;
        }

        $usuario = User::create([
            'name'              => 'Pindoor (perfiles sin activar)',
            'email'             => 'sistema@pindoor.cl',
            'password'          => Hash::make(Str::random(40)),
            'type'              => 'cliente',
            'es_sistema'        => true,
            'email_verified_at' => now(),
        ]);

        $this->info("✓ Usuario sistema creado: {$usuario->name} <{$usuario->email}>");

        return self::SUCCESS;
    }
}
