<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tightenco\Ziggy\BladeRouteGenerator;

class ResetZiggy
{
    /**
     * Ziggy prints the full route script once per process, then later @routes
     * calls only merge into the existing global. Pest's browser server reuses
     * one process, so a later full page load would reference Ziggy before it
     * exists. Reset at the start of each request; a second @routes in the
     * same request still merges.
     */
    public function handle(Request $request, Closure $next): Response
    {
        BladeRouteGenerator::$generated = false;

        return $next($request);
    }
}
