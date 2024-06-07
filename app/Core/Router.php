<?php

namespace App\Core;

use App\Http\Request;
use App\Http\Response;

class Router
{
  private static $routes = [];
  private static $currentGroupPrefix = '';
  private static $currentGroupMiddlewares = [];

  public static function get($url, $action)
  {
    self::add('GET', $url, $action, self::$currentGroupMiddlewares);
  }

  public static function post($url, $action)
  {
    self::add('POST', $url, $action, self::$currentGroupMiddlewares);
  }

  public static function any($url, $action)
  {
    $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];
    foreach ($methods as $method) {
      self::add($method, $url, $action, self::$currentGroupMiddlewares);
    }
  }

  private static function add($method, $url, $action, $middlewares)
  {
    $url = self::$currentGroupPrefix . $url;
    self::$routes[$method][$url] = [
      'action' => $action,
      'middlewares' => $middlewares
    ];
  }

  public static function group($prefix, $middlewares, $callback)
  {
    self::$currentGroupPrefix = $prefix;
    self::$currentGroupMiddlewares = is_array($middlewares) ? $middlewares : [$middlewares];
    call_user_func($callback);
    self::$currentGroupPrefix = '';
    self::$currentGroupMiddlewares = [];
  }

  public static function resolve()
  {
    $request = new Request();
    $response = new Response();

    $method = $request->getMethod();
    $basePath = $request->getBasePath();
    $routes = self::$routes[$method] ?? [];

    foreach ($routes as $routeUrl => $routeData) {
      // $pattern = "#^" . preg_replace('/\{[a-zA-Z0-9]+\}/', '([a-zA-Z0-9-]+)', $routeUrl) . "$#";
      $pattern = '#^' . preg_replace('/\{[a-zA-Z0-9]+\}/', '([a-zA-Z0-9\-_\.~!\$&\'\(\)\*\+,;=%:@]+)', $routeUrl) . '$#';
      if (preg_match($pattern, $basePath, $matches)) {
        array_shift($matches);

        foreach ($routeData['middlewares'] as $middlewareClass) {
          $response = self::handleMiddleware($middlewareClass, $request, $response);

          if ($response instanceof Response && $response->isRedirection()) {
            $response->send();
            return;
          }
        }

        $action = explode('@', $routeData['action']);
        $controllerName = "App\\Http\\Controllers\\" . $action[0];
        $controllerMethod = $action[1];

        $controller = new $controllerName();
        $response = call_user_func_array([$controller, $controllerMethod], array_merge([$request, $response], $matches));
        if($response instanceof Response){
          $response->send();
        }
        return;
      }
    }

    // If no matching route was found, handle as 404 error
    $response->setStatusCode(404);
    if ($request->expectsJson()) {
      $response->json(['success' => false, 'message' => 'NOT FOUND']);
    } else {
      $response->view('404', [], 'basic');
    }

    $response->send();
  }

  private static function handleMiddleware($middlewareClass, Request $request, Response $response)
  {
    $middlewareNamespace = "App\\Http\\Middlewares\\" . $middlewareClass;
    $middleware = new $middlewareNamespace();

    return $middleware->handle($request, $response, function ($request, $response) {
      return $response;
    });
  }
}
