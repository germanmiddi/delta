<?php

namespace Onmedia\NexusAgent;

use Throwable;

/**
 * Anota cada request y cada excepción de la app, para que `nexus:report`
 * resuma los últimos minutos.
 *
 * Un archivo por minuto y por tipo (`req-202610211230.log`), una línea por
 * evento, escrita con `FILE_APPEND | LOCK_EX`: no depende del driver de cache
 * de la app, no pierde eventos con requests concurrentes y cuesta un append.
 * El que crea el archivo de un minuto nuevo borra los viejos.
 *
 * Vive en `storage/framework/cache/nexus-agent`, que ya está en el
 * `.gitignore` de toda app Laravel.
 *
 * Nada de esto puede romper la app: si no se puede escribir, no se anota.
 *
 * ⚠️ Compatible con PHP 7.4 y Laravel 8: nada de sintaxis de PHP 8.
 */
class Recorder
{
    /** Minutos que se guardan; el reporte resume los últimos `WINDOW`. */
    public const KEEP_MINUTES = 15;

    public const WINDOW = 5;

    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/');
    }

    /**
     * Línea: `ms status usuario hora método ip ruta`. La ruta va última y
     * limpia (ver `path`); las líneas de 3 campos son de la versión 0.2.
     *
     * @param  string|null  $user  identificador del usuario logueado (se guarda hasheado)
     */
    public function request(int $ms, int $status, $user = null, string $method = '-', string $ip = '-', string $path = '/'): void
    {
        $who = $user === null ? '-' : substr(sha1((string) $user), 0, 12);
        $method = preg_match('/^[A-Z]{1,10}$/', $method) === 1 ? $method : '-';
        $ip = preg_match('/^[0-9A-Fa-f.:]{2,45}$/', $ip) === 1 ? $ip : '-';

        $this->append('req', $ms.' '.$status.' '.$who.' '.time().' '.$method.' '.$ip.' '.self::path($path));
    }

    /**
     * La ruta sin query string, sin espacios y con los segmentos que parecen
     * tokens (reset de clave, links firmados, hashes) tapados: lo que llega a
     * NEXUS dice qué se pidió, no con qué credencial.
     */
    public static function path(string $path): string
    {
        $path = (string) strtok($path, '?');
        $path = (string) preg_replace('/[\s\x00-\x1F\x7F]+/', '', $path);
        $path = (string) preg_replace('#(?<=/)[A-Za-z0-9_\-=.%]{24,}(?=/|$)#', '…', $path);

        $path = $path === '' ? '/' : $path;

        return strlen($path) > 200 ? substr($path, 0, 199).'…' : $path;
    }

    public function exception(Throwable $e): void
    {
        $this->append('exc', get_class($e));
    }

    /**
     * Las líneas de los últimos `WINDOW` minutos completos (sin el actual,
     * que todavía se está llenando). Null si nunca se anotó nada: la app no
     * recibe requests web o no puede escribir en storage.
     *
     * @return list<string>|null
     */
    public function lines(string $kind, ?int $now = null): ?array
    {
        if (! is_dir($this->path)) {
            return null;
        }

        $now = $now === null ? time() : $now;
        $lines = [];

        for ($i = 1; $i <= self::WINDOW; $i++) {
            $file = $this->file($kind, $now - $i * 60);

            if (is_file($file)) {
                $read = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $lines = array_merge($lines, $read === false ? [] : $read);
            }
        }

        return $lines;
    }

    /**
     * Las últimas `$limit` líneas, de la más nueva a la más vieja, incluido el
     * minuto en curso. Null si nunca se anotó nada.
     *
     * @return list<string>|null
     */
    public function recent(string $kind, int $limit, ?int $now = null): ?array
    {
        if (! is_dir($this->path)) {
            return null;
        }

        $now = $now === null ? time() : $now;
        $lines = [];

        for ($i = 0; $i <= self::WINDOW && count($lines) < $limit; $i++) {
            $file = $this->file($kind, $now - $i * 60);

            if (is_file($file)) {
                $read = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $lines = array_merge($lines, array_reverse($read === false ? [] : $read));
            }
        }

        return array_slice($lines, 0, $limit);
    }

    private function append(string $kind, string $line): void
    {
        try {
            $file = $this->file($kind, time());
            $new = ! is_file($file);

            if ($new && ! is_dir($this->path) && ! @mkdir($this->path, 0775, true) && ! is_dir($this->path)) {
                return;
            }

            @file_put_contents($file, $line."\n", FILE_APPEND | LOCK_EX);

            if ($new) {
                $this->prune();
            }
        } catch (Throwable $e) {
            // Medir nunca rompe la app.
        }
    }

    private function prune(): void
    {
        $limit = gmdate('YmdHi', time() - self::KEEP_MINUTES * 60);

        foreach ((array) @glob($this->path.'/*.log') as $file) {
            if (preg_match('/-(\d{12})\.log$/', (string) $file, $m) === 1 && $m[1] < $limit) {
                @unlink((string) $file);
            }
        }
    }

    private function file(string $kind, int $at): string
    {
        return $this->path.'/'.$kind.'-'.gmdate('YmdHi', $at).'.log';
    }
}
