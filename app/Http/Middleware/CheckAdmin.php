<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
      try {
        // Validar el token y autenticar al usuario
        $user = JWTAuth::parseToken()->authenticate();
    } catch (Exception $e) {
        if ($e instanceof TokenInvalidException) {
            return response()->json(['message' => 'Token inválido'], 401);
        } elseif ($e instanceof TokenExpiredException) {
            return response()->json(['message' => 'Token expirado'], 401);
        } else {
            return response()->json(['message' => 'Token no encontrado'], 401);
        }
    }

    return $next($request);
    }
}
