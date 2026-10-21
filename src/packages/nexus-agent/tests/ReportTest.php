<?php

namespace Onmedia\NexusAgent\Tests;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Onmedia\NexusAgent\NexusAgentServiceProvider;
use Orchestra\Testbench\TestCase;

class ReportTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [NexusAgentServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.name', 'Conectame');
        $app['config']->set('app.debug', false);
        $app['config']->set('nexus.url', 'https://nexusapi.test/');
        $app['config']->set('nexus.token', 'token-de-prueba');
    }

    public function test_it_schedules_itself_every_minute(): void
    {
        $events = collect($this->app->make(Schedule::class)->events())
            ->filter(function ($event) {
                return strpos((string) $event->command, 'nexus:report') !== false;
            });

        $this->assertCount(1, $events);
        $this->assertSame('* * * * *', $events->first()->expression);
    }

    public function test_it_sends_the_app_info_to_nexus(): void
    {
        Http::fake(['nexusapi.test/*' => Http::response(['ok' => true], 202)]);

        $this->artisan('nexus:report')->assertExitCode(0);

        Http::assertSent(function (Request $request) {
            $app = $request['app'];

            return $request->url() === 'https://nexusapi.test/api/agents/laravel'
                && $request->hasHeader('Authorization', 'Bearer token-de-prueba')
                && $request['agent_version'] === 2
                && $app['name'] === 'Conectame'
                && $app['debug'] === false
                && $app['laravel'] === $this->app->version()
                && $app['php'] === PHP_VERSION;
        });
    }

    public function test_without_config_it_sends_nothing_and_fails(): void
    {
        Http::fake();
        $this->app['config']->set('nexus.token', null);

        $this->artisan('nexus:report')->assertExitCode(1);

        Http::assertNothingSent();
    }

    public function test_nexus_rejecting_the_report_is_a_failure(): void
    {
        Http::fake(['nexusapi.test/*' => Http::response(['message' => 'Unauthenticated.'], 401)]);

        $this->artisan('nexus:report')->assertExitCode(1);
    }

    public function test_dry_run_does_not_send(): void
    {
        Http::fake();

        $this->artisan('nexus:report', ['--dry-run' => true])->assertExitCode(0);

        Http::assertNothingSent();
    }
}
