<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenIsValid extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle( $request, Closure $next, ...$guards): Response
    {
        $this->authenticate($request, $guards);
     
        // Optional: you can add more checks here in the future
        // Example: check if token is expired (if you implement expiration)
        
        return $next($request);
    }

    protected function unauthenticated($request, array $guards): never
    {
        abort(response()->json([
            'message' => 'Unauthenticated.',
            'status'  => 'error',
            'code'    => 401,
            'hint'    => 'Include Authorization: Bearer <token> header'
        ], 401));
    }

    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}
