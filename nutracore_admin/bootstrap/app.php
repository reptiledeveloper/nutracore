<?php

use App\Http\Middleware\AllowedModule;
use App\Http\Middleware\AuthAdmin;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Dotenv\Dotenv;


$customEnvPath = dirname(__DIR__) . '/config';
$customEnvFile = 'nutra_ad_hy1s3esd.env';
$dotenv = Dotenv::createImmutable($customEnvPath, $customEnvFile);
$dotenv->load();


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'authadmin' => AuthAdmin::class,
            'allowedmodule' => AllowedModule::class,
            'verified' => VerifyCsrfToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
