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
 * Nada de datos ni secretos: de los usuarios va solo cuántos hubo, de las
 * excepciones la clase, el archivo y el mensaje limpio (`Redact`: primera
 * línea, recortada, sin emails, claves, tokens, números largos ni SQL) y de
 * los últimos requests método, ruta limpia (sin query ni tokens), código, ms
 * e IP.
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
    public const PACKAGE = '0.4.0';

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
            'recent_requests' => $this->recentRequests(),
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
     * Los últimos requests web, del más nuevo al más viejo (minuto en curso
     * incluido). Null si están apagados o la app no anotó nada nunca.
     *
     * @return list<array<string, mixed>>|null
     */
    private function recentRequests(): ?array
    {
        $limit = (int) config('nexus.recent_requests', 50);
        if (! config('nexus.requests', true) || $limit <= 0) {
            return null;
        }

        $lines = $this->recorder->recent('req', min($limit, 100));
        if ($lines === null) {
            return null;
        }

        $recent = [];
        foreach ($lines as $line) {
            // Las líneas de 3 campos (paquete 0.2) no traen ruta ni hora.
            $parts = explode(' ', $line, 7);
            if (count($parts) < 7) {
                continue;
            }

            $recent[] = [
                'at' => gmdate('Y-m-d\TH:i:s\Z', (int) $parts[3]),
                'method' => $parts[4],
                'path' => $parts[6],
                'status' => (int) $parts[1],
                'ms' => (int) $parts[0],
                'ip' => $parts[5] === '-' ? null : $parts[5],
            ];
        }

        return $recent;
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

        $classes = [];
        foreach ($lines as $line) {
            $e = Recorder::parseException($line);
            if ($e !== null) {
                $classes[] = $e['class'];
            }
        }

        $byClass = array_count_values($classes);
        arsort($byClass);

        $top = [];
        foreach (array_slice($byClass, 0, 5, true) as $class => $count) {
            $top[] = ['class' => (string) $class, 'count' => $count];
        }

        return ['count' => count($classes), 'top' => $top, 'recent' => $this->recentExceptions()];
    }

    /**
     * Las últimas 20 excepciones, de la más nueva a la más vieja, con hora,
     * archivo y mensaje limpio: NEXUS las junta para mostrar las de 24 h (la
     * ventana del reporte es de minutos y el error se "borraba" solo).
     *
     * @return list<array<string, mixed>>
     */
    private function recentExceptions(): array
    {
        $out = [];
        foreach ((array) $this->recorder->recent('exc', 20) as $line) {
            $e = Recorder::parseException((string) $line);
            // Las líneas de la 0.3 (solo la clase) no traen hora.
            if ($e === null || $e['at'] === null) {
                continue;
            }

            $out[] = [
                'at' => gmdate('Y-m-d\TH:i:s\Z', $e['at']),
                'class' => substr($e['class'], 0, 255),
                'file' => $e['file'] === null ? null : substr($e['file'], 0, 255),
                'line' => $e['line'],
                'message' => $e['message'],
            ];
        }

        return $out;
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
            'failed_recent' => null,
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
                $out['failed_recent'] = $this->failedRecent(DB::connection(config('queue.failed.database'))->table($table));
            } catch (Throwable $e) {
                // Sin tabla de fallidos: va en null.
            }
        }

        return $out;
    }

    /**
     * Los últimos fallidos de las últimas 24 h, del más nuevo al más viejo:
     * qué job, en qué cola, la clase de la excepción, dónde se lanzó y el
     * mensaje limpio (`Redact`: de un proceso, solo el código de salida, porque
     * la línea de comando puede traer credenciales que el job recibió).
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return list<array<string, mixed>>
     */
    private function failedRecent($query): array
    {
        $rows = $query->select(['failed_at', 'queue', 'payload', 'exception'])
            ->where('failed_at', '>=', date('Y-m-d H:i:s', time() - 86400))
            ->orderByDesc('failed_at')
            ->limit(20)
            ->get();

        $out = [];
        foreach ($rows as $row) {
            $payload = json_decode((string) $row->payload, true);
            $job = is_array($payload) && isset($payload['displayName']) ? (string) $payload['displayName'] : '?';

            [$exception, $message, $file, $line] = $this->parseFailure((string) $row->exception);

            $out[] = [
                // failed_at está en la zona horaria de la app, igual que date().
                'at' => gmdate('Y-m-d\TH:i:s\Z', (int) strtotime((string) $row->failed_at)),
                'job' => substr($job, 0, 255),
                'queue' => substr((string) $row->queue, 0, 64),
                'exception' => substr($exception, 0, 255),
                'message' => Redact::message($exception, $message),
                'file' => $file === null ? null : substr(Redact::file($file, $this->app->basePath()), 0, 255),
                'line' => $line,
            ];
        }

        return $out;
    }

    /**
     * El texto de `failed_jobs.exception` es `(string) $e`: "Clase: mensaje in
     * /ruta/archivo.php:45" y después el stack trace. El mensaje puede tener
     * varias líneas; el "in archivo:línea" va al final de la última.
     *
     * @return array{0: string, 1: string, 2: string|null, 3: int|null}
     */
    private function parseFailure(string $text): array
    {
        $head = (string) preg_split('/\r?\nStack trace:/', $text, 2)[0];
        $class = preg_match('/^\s*([A-Za-z_\\\\][A-Za-z0-9_\\\\]*)/', $head, $m) === 1 ? $m[1] : '?';
        $rest = (string) substr(ltrim($head), strlen($class));
        $rest = (string) preg_replace('/^:\s?/', '', $rest);

        $file = null;
        $line = null;
        if (preg_match('/^(.*) in (\/\S+\.php):(\d+)\s*$/s', $rest, $m) === 1) {
            $rest = $m[1];
            $file = $m[2];
            $line = (int) $m[3];
        }

        return [$class, $rest, $file, $line];
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
