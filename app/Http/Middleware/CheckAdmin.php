<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
    Log::info('a',$request->all());
    try {
      $token = $this->getTokenFromRequest($request);

      if (!$token) {
        return response()->json(['error' => 'Token no encontrado'], 401);
      }

      JWTAuth::setToken($token);

      if (!$user = JWTAuth::authenticate()) {
        return response()->json(['error' => 'Usuario no autorizado'], 401);
      }

      $request->merge(['auth_user' => $user]);
      Log::info($request);
    } catch (TokenInvalidException $e) {
      return response()->json(['message' => 'Token inválido'], 401);
    } catch (TokenExpiredException $e) {
      return response()->json(['message' => 'Token expirado'], 401);
    } catch (Exception $e) {
      Log::error('Error JWT: ' . $e->getMessage());
      return response()->json(['message' => 'Token no encontrado'], 401);
    }

    return $next($request);
  }

  private function getTokenFromRequest(Request $request): ?string
  {
    Log::info($request);
    $token = $request->cookie('jwt_token');

    if (!$token && $request->header('Authorization')) {
      $token = str_replace('Bearer ', '', $request->header('Authorization'));
    }

    return $token;
  }
}
