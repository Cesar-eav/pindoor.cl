<?php

namespace App\Console\Commands;

use App\Services\PortaldiscImporter;
use Illuminate\Console\Command;

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
