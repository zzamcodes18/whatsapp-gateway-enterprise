<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rawKey = $request->header('X-API-Key') ?? $request->bearerToken() ?? $request->query('api_key');

        if (! $rawKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key required. Provide via X-API-Key header or Bearer Token.',
            ], 401);
        }

        $hash = hash('sha256', $rawKey);
        $apiKey = ApiKey::where('key_hash', $hash)
            ->where('is_active', true)
            ->with('user')
            ->first();

        if (! $apiKey || ! $apiKey->user || ! $apiKey->user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive API Key.',
            ], 401);
        }

        // Rate limiting per API Key
        $rateLimitKey = 'api_key_rate:'.$apiKey->id;
        $maxAttempts = $apiKey->rate_limit_per_minute ?? 60;

        if (RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            return response()->json([
                'success' => false,
                'message' => "Rate limit exceeded ({$maxAttempts} req/min). Try again in {$seconds} seconds.",
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($rateLimitKey, 60);

        // Update last used at timestamp silently
        $apiKey->timestamps = false;
        $apiKey->update(['last_used_at' => now()]);

        // Attach user and apiKey to request
        $request->setUserResolver(fn () => $apiKey->user);
        $request->attributes->set('api_key', $apiKey);

        return $next($request);
    }
}
