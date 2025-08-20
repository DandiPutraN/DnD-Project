<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestLogger
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Request Debug', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'cookies' => $request->cookies->all(),
            'session' => session()->all(),
        ]);

        return $next($request);
    }
}
