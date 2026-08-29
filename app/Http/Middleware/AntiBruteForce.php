<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AntiBruteForce
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 5, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Terlalu banyak percobaan. Harap tunggu {$seconds} detik lagi demi keamanan.",
                    'retry_after' => $seconds,
                ], 429);
            }

            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => "Terlalu banyak percobaan gagal. Akun/IP diamankan sementara. Silakan coba {$seconds} detik lagi.",
                ]);
        }

        return $next($request);
    }

    protected function resolveRequestSignature(Request $request): string
    {
        $email = Str::lower($request->input('email', 'guest'));

        return 'login_attempt:'.sha1($email.'|'.$request->ip());
    }
}
