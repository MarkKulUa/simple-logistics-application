<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRouteExecutionTime
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $response = $next($request);
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        Log::info('Route Execution Time', [
            'route' => $request->route() ? $request->route()->getName() : 'unknown',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'execution_time_ms' => $executionTime
        ]);

        return $response;
    }
}
