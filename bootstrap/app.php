<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        // Debugging setiap request
        $middleware->append(function (\Illuminate\Http\Request $request, \Closure $next) {
            Log::info('Request Debug', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'cookies' => $request->cookies->all(),
                'session' => session()->all(),
            ]);

            return $next($request);
        });
    })
    ->withExceptions(function ($exceptions) {
        // bisa ditambahkan custom exception handler di sini
    })
    ->create();
