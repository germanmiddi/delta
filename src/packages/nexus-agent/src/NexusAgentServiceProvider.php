<?php

namespace Onmedia\NexusAgent;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\ServiceProvider;
use Onmedia\NexusAgent\Console\ReportCommand;
use Throwable;

/**
 * Se registra solo (auto-discovery) y agenda el reporte en el scheduler de la
 * app: no hay que tocar `Kernel.php` (Laravel 8–10) ni `routes/console.php`
 * (11+).
 *
 * Que el reporte llegue cada minuto ya prueba que el scheduler de la app
 * corre: si deja de llegar, NEXUS lo marca caído.
 *
 * También escucha, sin middleware, cada request web (`RequestHandled`) y cada
 * excepción que la app reporta (`MessageLogged` con `exception`), y los anota
 * en `Recorder` para el reporte.
 *
 * ⚠️ Compatible con PHP 7.4 y Laravel 8: nada de sintaxis de PHP 8.
 */
class NexusAgentServiceProvider extends ServiceProvider
{
    private const ERROR_LEVELS = ['error', 'critical', 'alert', 'emergency'];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/nexus.php', 'nexus');

        $this->app->singleton(Recorder::class, function ($app) {
            $path = config('nexus.path');

            return new Recorder(is_string($path) && $path !== '' ? $path : $app->storagePath().'/framework/cache/nexus-agent');
        });
    }

    public function boot()
    {
        if (config('nexus.enabled')) {
            $this->listen();
        }

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([ReportCommand::class]);

        $this->publishes([
            __DIR__.'/../config/nexus.php' => $this->app->configPath('nexus.php'),
        ], 'nexus-config');

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            if (! config('nexus.enabled')) {
                return;
            }

            // En segundo plano: si NEXUS tarda, no demora las tareas de la app.
            $schedule->command('nexus:report')->everyMinute()->runInBackground();
        });
    }

    private function listen(): void
    {
        $events = $this->app['events'];

        $events->listen(MessageLogged::class, function (MessageLogged $event) {
            $exception = $event->context['exception'] ?? null;

            if ($exception instanceof Throwable && in_array($event->level, self::ERROR_LEVELS, true)) {
                $this->app->make(Recorder::class)->exception($exception);
            }
        });

        if (! config('nexus.requests', true)) {
            return;
        }

        $events->listen(RequestHandled::class, function (RequestHandled $event) {
            try {
                // REQUEST_TIME_FLOAT incluye el arranque de la app; con Octane
                // es por request, no por worker.
                $start = (float) $event->request->server('REQUEST_TIME_FLOAT', defined('LARAVEL_START') ? LARAVEL_START : microtime(true));
                $ms = (int) max(0, round((microtime(true) - $start) * 1000));

                $request = $event->request;

                // Los chequeos de NEXUS no son tráfico de la app: no cuentan.
                if ($this->isMonitor((string) $request->userAgent())) {
                    return;
                }

                $this->app->make(Recorder::class)->request(
                    $ms,
                    $event->response->getStatusCode(),
                    $this->userId(),
                    $request->getMethod(),
                    (string) $request->ip(),
                    $request->getPathInfo()
                );
            } catch (Throwable $e) {
                // Medir nunca rompe la app.
            }
        });
    }

    private function isMonitor(string $agent): bool
    {
        foreach ((array) config('nexus.ignore_user_agents', ['NEXUS-Monitor']) as $prefix) {
            if ($prefix !== '' && strpos($agent, (string) $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * El usuario logueado, solo si la request ya lo cargó: no dispara una
     * consulta a la base por medir.
     *
     * @return string|null
     */
    private function userId()
    {
        if (! $this->app->resolved('auth')) {
            return null;
        }

        $guard = $this->app['auth']->guard();

        if (! method_exists($guard, 'hasUser') || ! $guard->hasUser()) {
            return null;
        }

        $id = $guard->id();

        return $id === null ? null : get_class($guard->user()).':'.$id;
    }
}
