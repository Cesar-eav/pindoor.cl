<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CrearAdmin extends Command
{
    protected $signature   = 'admin:crear';
    protected $description = 'Crea el usuario administrador del sitio';

    public function handle(): int
    {
        $this->info('');
        $this->info('=== Crear usuario administrador ===');
        $this->info('');

        $nombre = $this->ask('Nombre');
        $email  = $this->ask('Email');

        if (User::where('email', $email)->exists()) {
            $this->error("Ya existe un usuario con el email {$email}.");
            return self::FAILURE;
        }

        $clave = $this->secret('Contraseña (mínimo 8 caracteres)');

        if (strlen($clave) < 8) {
            $this->error('La contraseña debe tener al menos 8 caracteres.');
            return self::FAILURE;
        }

        $confirmar = $this->secret('Confirmar contraseña');

        if ($clave !== $confirmar) {
            $this->error('Las contraseñas no coinciden.');
            return self::FAILURE;
        }

        $usuario = User::create([
            'name'     => $nombre,
            'email'    => $email,
            'password' => Hash::make($clave),
            'type'     => 'admin',
        ]);

        $this->info('');
        $this->info("✓ Administrador creado: {$usuario->name} <{$usuario->email}>");

        return self::SUCCESS;
    }
}
