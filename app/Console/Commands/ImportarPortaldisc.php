<?php

namespace App\Console\Commands;

use App\Models\Configuracion;
use App\Notifications\PanoramasImportados;
use App\Services\PortaldiscImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ImportarPortaldisc extends Command
{
    protected $signature = 'panoramas:importar-portaldisc
                            {--dry-run : Mostrar resultados sin guardar}';

    protected $description = 'Importa eventos de Valparaíso desde Portaldisc';

    public function handle(PortaldiscImporter $importer): int
    {
        $dryRun = $this->option('dry-run');

        $this->line('Consultando Portaldisc...');

        $resultado = $importer->importar($dryRun);

        if (!$dryRun) {
            try {
                Notification::route('telegram', Configuracion::telegramChatId())
                    ->notify(new PanoramasImportados(
                        'portaldisc',
                        count($resultado['creados']),
                        count($resultado['actualizados']),
                        count($resultado['omitidos'])
                    ));
            } catch (\Throwable $e) {
                Log::warning('ImportarPortaldisc — falló el aviso Telegram', ['error' => $e->getMessage()]);
            }
        }

        foreach ($resultado['creados'] as $ev) {
            $this->line("  {$ev['nombre']} — {$ev['fecha']} {$ev['hora']} — {$ev['lugar']}");
        }

        $this->newLine();
        $this->table(
            ['Creados', 'Actualizados', 'Omitidos'],
            [[count($resultado['creados']), count($resultado['actualizados']), count($resultado['omitidos'])]]
        );

        if ($dryRun) {
            $this->warn('Modo dry-run: ningún cambio guardado.');
        }

        return Command::SUCCESS;
    }
}
