<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\RequestLogger;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        // menambahkan middleware dengan class, bukan closure langsung
        $middleware->append(RequestLogger::class);
    })
    ->withExceptions(function ($exceptions) {
        // custom exception handler bisa ditambahkan di sini
    })
    ->create();
