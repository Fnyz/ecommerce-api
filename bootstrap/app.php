<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function(){

            // API routes with auth:sanctum middleware
            Route::prefix('api/v1')
                ->middleware(['api'])
                ->group(__DIR__.'/../routes/api/v1.php');

            // API routes without auth:sanctum middleware
            Broadcast::routes(['middleware' => ['auth:sanctum']]);

            // Load the channels file for broadcasting for private channels
            require base_path('routes/channels.php');
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ensure the IsAdmin middleware is registered so we can use it in our routes
        $middleware->alias(['admin' => \App\Http\Middleware\IsAdmin::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
