<?php

namespace Onmedia\NexusAgent\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Onmedia\NexusAgent\Report;
use Throwable;

/**
 * Arma el reporte de la app y lo manda a NEXUS. Lo corre el scheduler cada
 * minuto; también sirve a mano para probar la instalación.
 */
class ReportCommand extends Command
{
    protected $signature = 'nexus:report {--dry-run : Muestra el reporte sin mandarlo}';

    protected $description = 'Manda el estado de la app a NEXUS';

    public function handle(Report $report): int
    {
        $payload = $report->build();

        if ($this->option('dry-run')) {
            $this->line((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return 0;
        }

        $url = rtrim((string) config('nexus.url'), '/');
        $token = (string) config('nexus.token');

        if ($url === '' || $token === '') {
            $this->warn('NEXUS: faltan NEXUS_URL o NEXUS_TOKEN en el .env; no se manda nada.');

            return 1;
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout((int) config('nexus.timeout', 10))
                ->post($url.'/api/agents/laravel', $payload);
        } catch (Throwable $e) {
            $this->warn('NEXUS no responde: '.$e->getMessage());

            return 1;
        }

        if (! $response->successful()) {
            $this->warn('NEXUS no recibió el reporte (HTTP '.$response->status().')');

            return 1;
        }

        $this->info('Reporte enviado a NEXUS.');

        return 0;
    }
}
