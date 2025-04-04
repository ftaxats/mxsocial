<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;
class RateLimitMiddleware
{


    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $key = 'default', $maxAttempts = 5, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request, $key);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildRateLimitedResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes);

        $response = $next($request);

       return ($this->addRateLimitHeaders(
            $response,
            $key,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        ));
    }

    protected function resolveRequestSignature($request, $key)
    {
        if ($user = $request->user()) {
            return sha1($user->getAuthIdentifier());
        }

        if ($route = $request->route()) {
            return sha1($route->getDomain().'|'.$request->ip());
        }

        return sha1($request->ip());
    }

    protected function buildRateLimitedResponse($key, $maxAttempts)
    {
        $response = new Response('Too Many Attempts.', 429);
        $retryAfter = $this->limiter->availableIn($key);
        $response->headers->add([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
        ]);

        return $response;
    }

    protected function addRateLimitHeaders($response, $key, $maxAttempts, $remainingAttempts)
    {
        $retryAfter = $this->limiter->availableIn($key);
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
            'Retry-After' => $retryAfter,
        ]);

        return $response;
    }

    protected function calculateRemainingAttempts($key, $maxAttempts)
    {
        $remainingAttempts = $this->limiter->retriesLeft($key, $maxAttempts);

        return $remainingAttempts >= 0 ? $remainingAttempts : 0;
    }
}
