<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
//use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request ): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @param Closure $next
     * @param string|null ...$guards
     * @return Response
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        if (array_any($guards, fn($guard) => Auth::guard($guard)->check())) {
            return redirect($this->redirectTo($request));
        }

        return $next($request);
    }

    /**
     * Redirect user back to their previous page if they attempt to login to a guest only pages.
     * If user has attempted to login to a guest only page and has no previous page, redirect to home.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function redirectTo(Request $request): string
    {
        $previous = url()->previous();
        $appUrl = config('app.url');

        if (
            str_starts_with($previous, $appUrl) &&
            $previous !== $request->url()
        ) {
            return $previous;
        }

        return route('home');
    }
}
