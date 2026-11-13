<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// Простая защита дашборда по токену из конфига (config/dashboard.php)
class DashboardTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = config('dashboard.token');

        if (empty($token) || ! hash_equals((string) $token, (string) $request->query('token', ''))) {
            abort(403, 'Доступ запрещён');
        }

        return $next($request);
    }
}
