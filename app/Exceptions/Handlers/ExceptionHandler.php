<?php

namespace App\Exceptions\Handlers;

use App\Entity\Models\LogException;
use App\Exceptions\DatabaseExeption;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;
use App\Http\Models\Result;
use App\Http\Request;
use App\Http\Response;
use Exception;

class ExceptionHandler
{
  public static function handle(Exception $exception)
  {
    $request = new Request();
    $response = new Response();
    $res = new Result();

    if ($exception instanceof ForbiddenException) {

      $res->message = $exception->getMessage();
      $res->errorType = 'warning';
      $res->title = 'RESTRICCIÓN';

      self::respond($request, $response, $res, 403);
    } else if ($exception instanceof ValidationException) {
      $res->message = $exception->getMessage();
      $res->errorType = 'warning';
      $res->title = 'VALIDACIÓN';

      self::respond($request, $response, $res, 200);
    } else if ($exception instanceof DatabaseExeption) {
      $res->message = $exception->getMessage();
      $res->errorType = 'warning';
      $res->title = 'RESTRICCIÓN BASE DE DATOS';

      self::respond($request, $response, $res, 200);
    } else {
      $id = self::logExceptionToDatabase($request, $exception);
      $res->errorType = 'danger';
      $res->title = 'ERROR NO CONTROLADO';
      $res->message = 'Ha ocurrido un problema no controlado, por favor comuniquese con TI y proporcionele éste numero para que lo ayuden : ' . $id;

      self::respond($request, $response, $res, 500);
    }
  }

  private static function respond(Request $request, Response $response, Result $res, int $statusCode)
  {
    if ($request->expectsJson()) {
      $response->json($res, $statusCode)->send();
    } else {
      $viewName = $statusCode; // Assumes view names are the same as status codes
      $response->view($viewName, ['message' => $res->message], 'basic')->send();
    }
  }

  private static function logExceptionToDatabase(Request $request, Exception $exception)
  {
    try {
      $content = '';
      if ($request->expectsJson()) {
        $content = file_get_contents('php://input');
      } else {
        $content = json_encode($_POST);
      }

      $log = new LogException();
      $id = $log->insert([
        'content' => $content,
        'host' => HOST . PORT,
        'path' => URI,
        'stack' => $exception->getTraceAsString(),
        'message' => $exception->getMessage(),
        'created_at' => date('Y-m-d H:i:s'),
        'created_user' => $_SESSION[SESS_USER]['user_name'] ?? '',
      ], false);

      return $id;
    } catch (Exception $th) {
      if ($th->getCode() === '42S02') {
        return '0000000: ' . 'Tabla base o vista no encontrada';
      } else {
        return '9999999: ' . $th->getMessage();
      }
    }
  }
}
