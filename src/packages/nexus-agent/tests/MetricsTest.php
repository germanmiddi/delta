<?php

namespace Onmedia\NexusAgent\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Onmedia\NexusAgent\NexusAgentServiceProvider;
use Onmedia\NexusAgent\Recorder;
use Onmedia\NexusAgent\Report;
use Orchestra\Testbench\TestCase;
use RuntimeException;

/**
 * Lo que mide el paquete además de la ficha (F6.02, F6.03, F6.06, F6.07).
 */
class MetricsTest extends TestCase
{
    /** @var string */
    private $dir;

    protected function getPackageProviders($app)
    {
        return [NexusAgentServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->dir = sys_get_temp_dir().'/nexus-agent-test-'.uniqid();

        $app['config']->set('nexus.path', $this->dir);
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
    }

    protected function tearDown(): void
    {
        foreach ((array) glob($this->dir.'/*') as $file) {
            @unlink((string) $file);
        }
        @rmdir($this->dir);

        parent::tearDown();
    }

    /**
     * Lo anotado en el minuto actual, como lo va a ver el reporte del minuto
     * que viene.
     *
     * @return list<string>
     */
    private function recorded(string $kind): array
    {
        return (array) $this->app->make(Recorder::class)->lines($kind, time() + 60);
    }

    /**
     * Escribe líneas en el minuto anterior, que es lo que resume el reporte.
     *
     * @param  list<string>  $lines
     */
    private function previousMinute(string $kind, array $lines): void
    {
        @mkdir($this->dir, 0775, true);
        file_put_contents($this->dir.'/'.$kind.'-'.gmdate('YmdHi', time() - 60).'.log', implode("\n", $lines)."\n");
    }

    public function test_each_web_request_is_recorded_with_its_status_and_user(): void
    {
        Route::get('/ok', function () {
            return 'ok';
        });
        Route::get('/boom', function () {
            throw new RuntimeException('con datos del cliente');
        });

        $this->get('/ok')->assertOk();
        $this->get('/boom')->assertStatus(500);

        $user = new User();
        $user->forceFill(['id' => 42]);
        $this->actingAs($user)->get('/ok')->assertOk();

        $lines = $this->recorded('req');
        $this->assertCount(3, $lines);
        $this->assertMatchesRegularExpression('#^\d+ 200 - \d{10} GET 127\.0\.0\.1 /ok$#', $lines[0]);
        $this->assertMatchesRegularExpression('#^\d+ 500 - \d{10} GET 127\.0\.0\.1 /boom$#', $lines[1]);
        $this->assertMatchesRegularExpression('#^\d+ 200 [0-9a-f]{12} #', $lines[2]);
        $this->assertStringNotContainsString('42', explode(' ', $lines[2])[2], 'el usuario va hasheado');

        // La excepción del 500 se anota por su clase, sin el mensaje.
        $this->assertSame([RuntimeException::class], $this->recorded('exc'));
    }

    public function test_logged_exceptions_count_but_plain_errors_and_warnings_do_not(): void
    {
        Log::error('algo falló', ['exception' => new RuntimeException('secreto')]);
        Log::warning('aviso', ['exception' => new RuntimeException('no cuenta')]);
        Log::error('sin excepción');

        $this->assertSame([RuntimeException::class], $this->recorded('exc'));
    }

    public function test_requests_can_be_turned_off(): void
    {
        $this->app['config']->set('nexus.requests', false);

        $this->assertNull($this->app->make(Report::class)->build()['requests']);
    }

    public function test_the_report_summarizes_the_last_minutes(): void
    {
        $this->previousMinute('req', ['100 200 -', '300 200 aaaaaaaaaaaa', '50 404 aaaaaaaaaaaa', '900 500 bbbbbbbbbbbb', 'basura']);
        $this->previousMinute('exc', ['RuntimeException', 'RuntimeException', 'LogicException']);

        $report = $this->app->make(Report::class)->build();

        $this->assertSame([
            'count' => 4,
            'per_minute' => 0.8,
            'status_4xx' => 1,
            'status_5xx' => 1,
            'p50_ms' => 100,
            'p95_ms' => 900,
            'max_ms' => 900,
            'users' => 2,
        ], $report['requests']);

        $this->assertSame([
            'count' => 3,
            'top' => [
                ['class' => 'RuntimeException', 'count' => 2],
                ['class' => 'LogicException', 'count' => 1],
            ],
        ], $report['errors']);
    }

    public function test_the_last_requests_go_newest_first_with_a_clean_path(): void
    {
        Route::get('/reset-password/{token}', function () {
            return 'ok';
        });
        Route::post('/pedidos', function () {
            return 'ok';
        });

        $this->get('/reset-password/'.str_repeat('a1B2', 10).'?email=cliente@example.com')->assertOk();
        $this->post('/pedidos?token=secreto')->assertOk();
        // Una línea de la versión 0.2: cuenta en el resumen, no en la lista.
        $this->previousMinute('req', ['100 200 -']);

        $recent = $this->app->make(Report::class)->build()['recent_requests'];

        $this->assertCount(2, $recent);
        $this->assertSame(['POST', '/pedidos', 200, '127.0.0.1'], [$recent[0]['method'], $recent[0]['path'], $recent[0]['status'], $recent[0]['ip']]);
        $this->assertSame('/reset-password/…', $recent[1]['path']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d\d-\d\dT\d\d:\d\d:\d\dZ$/', $recent[0]['at']);
        $this->assertStringNotContainsString('secreto', json_encode($recent));
        $this->assertStringNotContainsString('example.com', json_encode($recent));
    }

    public function test_the_nexus_checks_are_not_recorded(): void
    {
        Route::get('/', function () {
            return 'ok';
        });

        $this->withHeader('User-Agent', 'NEXUS-Monitor/1.0')->get('/')->assertOk();
        $this->withHeader('User-Agent', 'Mozilla/5.0')->get('/')->assertOk();

        $this->assertCount(1, $this->recorded('req'));
    }

    public function test_the_last_requests_can_be_turned_off(): void
    {
        $this->app['config']->set('nexus.recent_requests', 0);
        $this->app->make(Recorder::class)->request(10, 200, null, 'GET', '10.0.0.1', '/x');

        $report = $this->app->make(Report::class)->build();

        $this->assertNull($report['recent_requests']);
        $this->assertNotNull($report['requests']);
    }

    public function test_the_path_hides_tokens_and_query(): void
    {
        $this->assertSame('/email/verify/12/…', Recorder::path('/email/verify/12/'.sha1('x').'?expires=1&signature=abc'));
        $this->assertSame('/', Recorder::path(''));
        $this->assertSame('/api/v1/orders/1234', Recorder::path('/api/v1/orders/1234'));
        $this->assertSame(200, strlen(Recorder::path('/'.str_repeat('ab/', 100))) - 2);
    }

    public function test_without_anything_recorded_traffic_and_errors_are_unknown(): void
    {
        $report = $this->app->make(Report::class)->build();

        $this->assertNull($report['requests']);
        $this->assertNull($report['errors']);
    }

    public function test_old_minutes_are_pruned(): void
    {
        @mkdir($this->dir, 0775, true);
        $old = $this->dir.'/req-'.gmdate('YmdHi', time() - (Recorder::KEEP_MINUTES + 5) * 60).'.log';
        file_put_contents($old, "1 200 -\n");

        $this->app->make(Recorder::class)->request(10, 200);

        $this->assertFileDoesNotExist($old);
    }

    public function test_queue_pending_and_failed_jobs(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        $this->app['config']->set('queue.default', 'database');
        $this->app['config']->set('queue.connections.database', ['driver' => 'database', 'table' => 'jobs', 'queue' => 'default', 'retry_after' => 90]);
        $this->app['config']->set('queue.failed', ['driver' => 'database-uuids', 'database' => 'testing', 'table' => 'failed_jobs']);

        $job = ['payload' => '{}', 'attempts' => 0, 'available_at' => time(), 'created_at' => time()];
        DB::table('jobs')->insert([['queue' => 'default'] + $job, ['queue' => 'default'] + $job, ['queue' => 'mails'] + $job]);

        $failed = ['connection' => 'database', 'queue' => 'default', 'payload' => '{"displayName":"App\\\\Jobs\\\\SyncAfip"}', 'exception' => "Exception: Error con la clave secreta123\n#0 /app/Jobs/SyncAfip.php(10)"];
        DB::table('failed_jobs')->insert([
            ['uuid' => 'a', 'failed_at' => date('Y-m-d H:i:s', time() - 600)] + $failed,
            ['uuid' => 'b', 'failed_at' => date('Y-m-d H:i:s', time() - 7200)] + $failed,
            ['uuid' => 'c', 'failed_at' => date('Y-m-d H:i:s', time() - 90000)] + $failed,
        ]);

        $queue = $this->app->make(Report::class)->build()['queue'];
        $recent = $queue['failed_recent'];
        unset($queue['failed_recent']);
        $this->assertSame(['connection' => 'database', 'driver' => 'database', 'pending' => ['default' => 2], 'failed_total' => 3, 'failed_last_hour' => 1], $queue);

        // Los de las últimas 24 h, del más nuevo al más viejo, sin el mensaje.
        $this->assertCount(2, $recent);
        $this->assertSame(['job' => 'App\\Jobs\\SyncAfip', 'queue' => 'default', 'exception' => 'Exception'], array_diff_key($recent[0], ['at' => 1]));
        $this->assertSame(gmdate('Y-m-d\TH:i', time() - 600), substr($recent[0]['at'], 0, 16));
        $this->assertStringNotContainsString('secreta123', json_encode($recent));

        $this->app['config']->set('nexus.queues', ['default', 'mails']);
        $this->assertSame(['default' => 2, 'mails' => 1], $this->app->make(Report::class)->build()['queue']['pending']);
    }

    public function test_sync_queue_has_nothing_pending_to_measure(): void
    {
        $this->app['config']->set('queue.default', 'sync');

        $queue = $this->app->make(Report::class)->build()['queue'];

        $this->assertSame('sync', $queue['driver']);
        $this->assertNull($queue['pending']);
    }

    public function test_the_database_answers(): void
    {
        $db = $this->app->make(Report::class)->build()['db'];
        $this->assertTrue($db['ok']);
        $this->assertIsInt($db['ms']);

        $this->app['config']->set('database.connections.testing.database', '/no/existe/nexus.sqlite');
        DB::purge('testing');

        $db = $this->app->make(Report::class)->build()['db'];
        $this->assertFalse($db['ok']);
        $this->assertNotNull($db['error']);
    }
}
