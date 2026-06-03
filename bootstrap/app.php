<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'admin'       => \App\Http\Middleware\IsAdmin::class,
            'mentor'      => \App\Http\Middleware\IsMentor::class,
            'institution' => \App\Http\Middleware\IsInstitution::class,
            'basic.auth'  => \App\Http\Middleware\BasicAuth::class,
            'api.key'     => \App\Http\Middleware\ApiKeyAuth::class,
            'onboarded'   => \App\Http\Middleware\EnsureOnboardingCompleted::class,
            'assessed'    => \App\Http\Middleware\EnsureAssessmentCompleted::class,
        ]);

        $middleware->api(remove: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
