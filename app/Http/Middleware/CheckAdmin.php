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
    $token = null;

    try {
      // 1. Intenta obtener el token del header Authorization
      $authorizationHeader = $request->header('Authorization');
      if ($authorizationHeader) {
        $parts = explode(' ', trim($authorizationHeader), 2);
        if (count($parts) === 2) {
          // Si hay un esquema (ej: "Bearer <token>", "Custom <token>")
          $token = trim($parts[1]);
        } else {
          // Si no hay esquema, usa el header completo como token
          $token = trim($authorizationHeader);
        }
      }

      // 2. Si no se encuentra en el header, intenta obtenerlo de la cookie 'jwt_token'
      if (!$token) {
        $token = $request->cookie('jwt_token');
      }

      // 3. Autentica si se encontró un token
      if ($token) {
        try {
          JWTAuth::setToken($token);
          if ($user = JWTAuth::authenticate()) {
            $request->merge(['auth_user' => $user]);
          }
          // Si authenticate() falla (token inválido o usuario no encontrado),
          // $user será false, y la solicitud continuará sin autenticación.
        } catch (TokenInvalidException $e) {
          Log::warning('Middleware: Token inválido', ['token' => $token, 'exception' => $e->getMessage()]);
          // Token inválido, la solicitud continúa sin autenticación
        } catch (TokenExpiredException $e) {
          Log::warning('Middleware: Token expirado', ['token' => $token, 'exception' => $e->getMessage()]);
          // Token expirado, la solicitud continúa sin autenticación
        } catch (Exception $e) {
          Log::error('Middleware: Error al autenticar token', ['token' => $token, 'exception' => $e->getMessage()]);
          // Otro error al autenticar, la solicitud continúa sin autenticación
        }
      }

      // 4. La solicitud siempre continúa, con o sin usuario autenticado
      return $next($request);
    } catch (\Throwable $fatalError) {
      // Captura errores fatales que puedan ocurrir en el middleware
      Log::critical('Middleware: Error fatal', ['exception' => $fatalError->getMessage(), 'trace' => $fatalError->getTraceAsString()]);
      // En un error fatal, podrías optar por abortar la solicitud con un error 500
      return response()->json(['error' => 'Error interno del servidor'], 500);
    }
  }
}
