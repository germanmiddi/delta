<?php

namespace Onmedia\NexusAgent;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Throwable;

/**
 * El reporte que va a NEXUS cada minuto: la ficha de la app, el tráfico y los
 * errores de los últimos minutos (de `Recorder`), la cola y la base.
 *
 * Nada de usuarios, datos ni secretos: de los usuarios va solo cuántos hubo,
 * y de las excepciones, la clase (no el mensaje, que puede traer datos).
 *
 * Cada parte se mide por separado: si una falla, va en null y el resto sale
 * igual.
 *
 * ⚠️ Compatible con PHP 7.4 y Laravel 8: nada de sintaxis de PHP 8.
 */
class Report
{
    /** Versión del formato del reporte (la valida NEXUS). */
    public const VERSION = 2;

    /** Versión del paquete; se sube con cada `bin/publicar.sh`. */
    public const PACKAGE = '0.2.0';

    /** @var Application */
    private $app;

    /** @var Recorder */
    private $recorder;

    public function __construct(Application $app, Recorder $recorder)
    {
        $this->app = $app;
        $this->recorder = $recorder;
    }

    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        return [
            'agent_version' => self::VERSION,
            'package_version' => self::PACKAGE,
            'reported_at' => gmdate('Y-m-d\TH:i:s\Z'),
            'window_minutes' => Recorder::WINDOW,
            'app' => [
                'name' => (string) config('app.name'),
                'env' => $this->app->environment(),
                'debug' => (bool) config('app.debug'),
                'url' => (string) config('app.url'),
                'laravel' => $this->app->version(),
                'php' => PHP_VERSION,
            ],
            'requests' => $this->requests(),
            'errors' => $this->errors(),
            'queue' => $this->queue(),
            'db' => $this->db(),
        ];
    }

    /**
     * Requests web de los últimos minutos: cantidad, 4xx/5xx, latencia y
     * usuarios logueados distintos. Null si la app no anotó nada nunca.
     *
     * @return array<string, mixed>|null
     */
    private function requests(): ?array
    {
        if (! config('nexus.requests', true)) {
            return null;
        }

        $lines = $this->recorder->lines('req');
        if ($lines === null) {
            return null;
        }

        $ms = [];
        $users = [];
        $s4xx = 0;
        $s5xx = 0;

        foreach ($lines as $line) {
            $parts = explode(' ', $line);
            if (count($parts) < 3) {
                continue;
            }

            $ms[] = (int) $parts[0];
            $status = (int) $parts[1];
            $s4xx += $status >= 400 && $status < 500 ? 1 : 0;
            $s5xx += $status >= 500 ? 1 : 0;

            if ($parts[2] !== '-') {
                $users[$parts[2]] = true;
            }
        }

        sort($ms);
        $count = count($ms);

        return [
            'count' => $count,
            'per_minute' => round($count / Recorder::WINDOW, 1),
            'status_4xx' => $s4xx,
            'status_5xx' => $s5xx,
            'p50_ms' => $this->percentile($ms, 50),
            'p95_ms' => $this->percentile($ms, 95),
            'max_ms' => $count > 0 ? $ms[$count - 1] : null,
            'users' => count($users),
        ];
    }

    /**
     * Excepciones reportadas (web, colas y comandos) de los últimos minutos,
     * con las clases más repetidas.
     *
     * @return array<string, mixed>|null
     */
    private function errors(): ?array
    {
        $lines = $this->recorder->lines('exc');
        if ($lines === null) {
            return null;
        }

        $byClass = array_count_values($lines);
        arsort($byClass);

        $top = [];
        foreach (array_slice($byClass, 0, 5, true) as $class => $count) {
            $top[] = ['class' => (string) $class, 'count' => $count];
        }

        return ['count' => count($lines), 'top' => $top];
    }

    /**
     * Jobs pendientes por cola y fallidos (total y última hora).
     *
     * @return array<string, mixed>
     */
    private function queue(): array
    {
        $connection = (string) config('queue.default');
        $driver = (string) config('queue.connections.'.$connection.'.driver', $connection);

        $out = [
            'connection' => $connection,
            'driver' => $driver,
            'pending' => null,
            'failed_total' => null,
            'failed_last_hour' => null,
        ];

        if ($driver !== 'sync' && $driver !== 'null') {
            $queues = (array) config('nexus.queues', []);
            if ($queues === []) {
                $queues = [(string) config('queue.connections.'.$connection.'.queue', 'default')];
            }

            try {
                $pending = [];
                foreach ($queues as $queue) {
                    $pending[(string) $queue] = (int) Queue::connection($connection)->size((string) $queue);
                }
                $out['pending'] = $pending;
            } catch (Throwable $e) {
                // Cola inaccesible: va en null, NEXUS lo muestra sin dato.
            }
        }

        $failer = (string) config('queue.failed.driver', 'database');
        $table = config('queue.failed.table');

        if (in_array($failer, ['database', 'database-uuids'], true) && is_string($table) && $table !== '') {
            try {
                $query = DB::connection(config('queue.failed.database'))->table($table);
                $out['failed_total'] = (int) $query->count();
                $out['failed_last_hour'] = (int) DB::connection(config('queue.failed.database'))->table($table)
                    ->where('failed_at', '>=', date('Y-m-d H:i:s', time() - 3600))
                    ->count();
            } catch (Throwable $e) {
                // Sin tabla de fallidos: va en null.
            }
        }

        return $out;
    }

    /**
     * La base responde, y en cuánto. De un error va solo la clase: el mensaje
     * puede traer host y usuario.
     *
     * @return array<string, mixed>
     */
    private function db(): array
    {
        $start = microtime(true);

        try {
            DB::connection()->select('select 1');

            return ['ok' => true, 'ms' => (int) round((microtime(true) - $start) * 1000), 'error' => null];
        } catch (Throwable $e) {
            return ['ok' => false, 'ms' => null, 'error' => get_class($e)];
        }
    }

    /**
     * @param  list<int>  $sorted
     */
    private function percentile(array $sorted, int $p): ?int
    {
        $count = count($sorted);
        if ($count === 0) {
            return null;
        }

        return $sorted[(int) max(0, ceil($count * $p / 100) - 1)];
    }
}
