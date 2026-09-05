<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CaptureUtmMiddleware
{
    public const KEYS = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'utm_referrer',
    ];

    public const COOKIE_NAME = 'utm';
    public const COOKIE_TTL_MINUTES = 60 * 24 * 35;

    public function handle(Request $request, Closure $next): Response
    {
        $incoming = array_filter(
            $request->only(self::KEYS),
            fn ($v) => filled($v) && is_string($v)
        );

        if ($incoming) {
            $incoming = array_intersect_key($incoming, array_flip(self::KEYS));

            session([
                'utm' => array_merge((array) session('utm', []), $incoming),
            ]);

            $stored = json_decode((string) $request->cookie(self::COOKIE_NAME, '{}'), true) ?: [];
            $merged = array_filter(
                array_merge($stored, $incoming),
                fn ($v) => filled($v) && is_string($v)
            );

            Cookie::queue(
                Cookie::make(
                    self::COOKIE_NAME,
                    json_encode($merged),
                    self::COOKIE_TTL_MINUTES,
                    '/',
                    null,
                    false,
                    false,
                    false,
                    'lax'
                )
            );
        }

        return $next($request);
    }
}
