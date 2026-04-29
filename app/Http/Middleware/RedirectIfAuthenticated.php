<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\{RedirectResponse, Request, Response};
use Illuminate\Support\Facades\Auth;
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * If the user is already authenticated, this middleware prevents access to guest-only routes
     * (like login/register) by redirecting them back to their previous page or a fallback home route.
     * To avoid broken experiences, redirects only trigger for "full page" GET requests (HTML or Inertia).
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @param  string|null  ...$guards
     * @return RedirectResponse|Response
     */
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($request->isMethod('GET') && (!$request->expectsJson() || $request->hasHeader('X-Inertia'))) {
                    return redirect($this->redirectTo($request));
                }

                return $next($request);
            }
        }

        return $next($request);
    }

    /**
     * Determine the URL to redirect the authenticated user to.
     *
     * This function cycles through the session-stored 'url_history' stack to find a
     * suitable previous page. It pops invalid or redundant URLs until a valid internal
     * URL is found. If the history is exhausted, it defaults to the home route.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function redirectTo(Request $request): string
    {
        try {
            $history = session()->get('url_history', []);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return route('home');
        }

        // Use the actual request root to ensure compatibility with different domains/environments
        $appUrl = $request->getSchemeAndHttpHost();

        while (!empty($history)) {
            $previous = array_pop($history);

            session()->put('url_history', $history);

            if (
                str_starts_with($previous, $appUrl) &&
                $previous !== $request->fullUrl()
            ) {
                return $previous;
            }
        }

        return route('home');
    }
}
