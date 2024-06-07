<?php

namespace App\Http\Middlewares;

use App\Exceptions\ForbiddenException;
use App\Http\Request;
use App\Http\Response;
use Closure;

class AdminAuthMiddleware
{
  public function handle(Request $request, Response $response, Closure $next)
  {
    $user = $_SESSION[SESS_USER];

    if (!isset($_SESSION[SESS_USER]['id'])) {
      if ($request->expectsJson()) {
        throw new ForbiddenException('La sesion a expirado o no tienes permisos para acceder a estos recursos');
      } else {
        // Si el usuario no está autenticado, redirigir al login.
        return $response->redirect('/user/login');
      }
    }

    // $_SESSION[SESS_USER]['config_shard'] = NULL;

    // Si el usuario está autenticado, sigue con el siguiente middleware o controlador.
    return $next($request, $response);
  }
}
