<?php

namespace Onmedia\NexusAgent\Tests;

use Onmedia\NexusAgent\Redact;
use PHPUnit\Framework\TestCase;

/**
 * El mensaje de un error que sale hacia NEXUS: útil, pero sin datos.
 */
class RedactTest extends TestCase
{
    public function test_a_process_failure_only_sends_its_exit_code(): void
    {
        $message = "The command \"'python3' 'scrape.py' '--cuit' '20123456789' '--clave' 'MiClave!'\" failed.\n\nExit Code: 1(General error)\n\nWorking directory: /var/www";

        $this->assertSame('Exit Code: 1(General error)', Redact::message('Symfony\\Component\\Process\\Exception\\ProcessFailedException', $message));
        $this->assertNull(Redact::message('Symfony\\Component\\Process\\Exception\\ProcessTimedOutException', 'The process "scrape --clave X" exceeded the timeout of 60 seconds.'));
    }

    public function test_sql_errors_keep_the_sqlstate_but_not_the_query(): void
    {
        $message = "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry (Connection: mysql, SQL: insert into users (email) values (ana@x.com))";

        $this->assertSame('SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry', Redact::message('Illuminate\\Database\\QueryException', $message));
        $this->assertSame('SQLSTATE[HY000]: General error', Redact::message('Illuminate\\Database\\QueryException', 'SQLSTATE[HY000]: General error (SQL: select * from x where dni = 30111222)'));
    }

    public function test_personal_data_and_secrets_are_covered(): void
    {
        $this->assertSame('No se pudo notificar a ‹email›', Redact::message('Exception', 'No se pudo notificar a juan.perez@cliente.com.ar'));
        $this->assertSame('CUIT ‹#› sin certificado', Redact::message('Exception', 'CUIT 20-12345678-9 sin certificado'));
        $this->assertSame('Tel ‹#›', Redact::message('Exception', 'Tel 011 4555 6677'));
        $this->assertSame('Auth failed: Bearer ‹…›', Redact::message('Exception', 'Auth failed: Bearer eyJhbGciOi.abc.def'));
        $this->assertSame('GET https://api.afip.gob.ar/ws?‹…› timed out', Redact::message('Exception', 'GET https://api.afip.gob.ar/ws?token=abc&cuit=1 timed out'));
        $this->assertSame('password=‹…› rejected', Redact::message('Exception', 'password="hunter2" rejected'));
        $this->assertSame('key ‹…› invalid', Redact::message('Exception', 'key '.str_repeat('a1B2', 10).' invalid'));
    }

    public function test_useful_text_survives(): void
    {
        $this->assertSame('Servicio informado inexistente', Redact::message('Exception', 'Servicio informado inexistente'));
        $this->assertSame('Undefined array key "cuit"', Redact::message('ErrorException', 'Undefined array key "cuit"'));
        $this->assertSame('HTTP 404 en la línea 12', Redact::message('Exception', 'HTTP 404 en la línea 12'));
    }

    public function test_only_the_first_line_and_up_to_300_chars(): void
    {
        $this->assertSame('primera', Redact::message('Exception', "primera\nsegunda con datos"));
        $this->assertSame(300, mb_strlen((string) Redact::message('Exception', str_repeat('palabra ', 100))));
        $this->assertNull(Redact::message('Exception', '   '));
    }

    public function test_files_are_relative_to_the_app(): void
    {
        $this->assertSame('app/Jobs/X.php', Redact::file('/var/www/evm/app/Jobs/X.php', '/var/www/evm'));
        $this->assertSame('Lib.php', Redact::file('/otro/lado/Lib.php', '/var/www/evm'));
    }
}
