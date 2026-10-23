<?php

namespace Onmedia\NexusAgent;

/**
 * Lo que se manda a NEXUS de un error: el mensaje, limpio de datos.
 *
 * El mensaje de una excepción puede traer credenciales, SQL con valores,
 * emails o CUITs. Sin él, el detalle dice solo "Exception" y no sirve para
 * nada, así que va recortado y tapado:
 *
 * - Solo la primera línea, hasta 300 caracteres.
 * - Las excepciones de procesos (`ProcessFailedException`…) llevan la línea
 *   de comando, que puede traer claves (EVM le pasa la del contribuyente al
 *   scraper): de esas va solo el código de salida.
 * - De un error de SQL va el SQLSTATE, sin la consulta (trae los valores).
 * - Se tapan emails, `clave=valor` de cosas secretas, tokens largos, números
 *   de 6 o más cifras (CUIT, DNI, tarjetas, teléfonos) y el query string de
 *   las URLs.
 *
 * ⚠️ Compatible con PHP 7.4 y Laravel 8: nada de sintaxis de PHP 8.
 */
class Redact
{
    public const MAX = 300;

    private const PROCESS = '/Process(Failed|TimedOut|Signaled)Exception$/';

    public static function message(string $class, string $message): ?string
    {
        if (preg_match(self::PROCESS, $class) === 1) {
            return preg_match('/Exit Code:\s*(\d+)(\s*\([^)\r\n]*\))?/', $message, $m) === 1
                ? 'Exit Code: '.$m[1].(isset($m[2]) ? $m[2] : '')
                : null;
        }

        $text = trim((string) strtok($message, "\r\n"));

        // SQL: "SQLSTATE[…]: … (Connection: mysql, SQL: insert … values (…))".
        $text = (string) preg_replace('/\s*\((?:Connection: [^,]*, )?SQL: .*$/s', '', $text);
        $text = (string) preg_replace('/\s*\(SQL: .*$/s', '', $text);

        $text = (string) preg_replace('/[\x00-\x1F\x7F]+/', ' ', $text);
        $text = (string) preg_replace('/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/', '‹email›', $text);
        $text = (string) preg_replace('/\b(password|passwd|pwd|secret|token|api[_-]?key|clave|contraseña|authorization)(\s*[=:]\s*)("[^"]*"|\'[^\']*\'|\S+)/iu', '$1$2‹…›', $text);
        // "con la clave X": la palabra que sigue también puede ser la clave.
        $text = (string) preg_replace('/\b(password|passwd|clave|contraseña)(\s+)(?!‹)("[^"]*"|\'[^\']*\'|\S+)/iu', '$1$2‹…›', $text);
        $text = (string) preg_replace('/\b(Bearer|Basic)\s+\S+/i', '$1 ‹…›', $text);
        $text = (string) preg_replace('#(https?://[^\s?"\']+)\?[^\s"\']*#', '$1?‹…›', $text);
        $text = (string) preg_replace('/[A-Za-z0-9_\-+\/=]{32,}/', '‹…›', $text);
        $text = (string) preg_replace('/\d[\d.\-\/]{4,}\d|\d{3,}(?: \d{3,})+/', '‹#›', $text);

        $text = trim($text);
        if ($text === '') {
            return null;
        }

        return mb_strlen($text) > self::MAX ? mb_substr($text, 0, self::MAX - 1).'…' : $text;
    }

    /**
     * La ruta relativa a la app (`app/Jobs/X.php`): sin el path del servidor.
     */
    public static function file(string $file, string $base): string
    {
        $base = rtrim($base, '/').'/';

        return strpos($file, $base) === 0 ? substr($file, strlen($base)) : basename($file);
    }
}
