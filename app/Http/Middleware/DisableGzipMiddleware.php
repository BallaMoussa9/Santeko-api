<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableGzipMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cette ligne est la clé. Elle supprime l'en-tête Content-Encoding
        // pour empêcher le navigateur de tenter la décompression.
        $response->headers->remove('Content-Encoding');

        return $response;
    }
}