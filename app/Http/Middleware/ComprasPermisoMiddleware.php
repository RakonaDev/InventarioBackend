<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ComprasPermisoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->get('auth_user');

        if ($user && $user->roles && $user->roles->ListPaginas()->where('nombre', 'compras')->exists()) {
            return $next($request);
        }
        $cookie = cookie(
            'jwt_token',
            '',
            0,
            '/',
            ENV("DOMAIN_COOKIE"),
            true,
            true,
            false,
            'None'
        );
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(
            ['error' => 'No tienes permiso para acceder a esta sección de categorías'],
            401
        )->withCookie($cookie);
    }
}
