<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$allowed): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (empty($allowed)) {
            return $next($request);
        }

        $role = strtolower(trim((string) ($user->role ?? $user->Role ?? '')));
        $username = strtolower(trim((string) ($user->username ?? $user->UserName ?? '')));

        $allowedNormalized = array_values(array_filter(array_map(
            static fn (string $value): string => strtolower(trim($value)),
            $allowed
        )));

        if (in_array($role, $allowedNormalized, true) || in_array($username, $allowedNormalized, true)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return redirect()->route('login')->with('error', 'Access denied.');
    }
}
