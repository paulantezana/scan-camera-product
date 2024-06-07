<?php

namespace App\Http;

class Response
{
  private $content;
  private $statusCode;
  private $headers;

  public function __construct($content = '', $statusCode = 200, $headers = [])
  {
    $this->content = $content;
    $this->statusCode = $statusCode;
    $this->headers = $headers;
  }

  public function isRedirection(): bool
  {
    return array_key_exists('Location', $this->headers);
  }

  public function setContent($content): self
  {
    $this->content = $content;
    return $this;
  }

  public function setStatusCode(int $statusCode): self
  {
    $this->statusCode = $statusCode;
    return $this;
  }

  public function setHeader(string $name, string $value): self
  {
    $this->headers[$name] = $value;
    return $this;
  }

  public function send()
  {
    http_response_code($this->statusCode);

    foreach ($this->headers as $name => $value) {
      header($name . ': ' . $value);
    }

    echo $this->content;
  }

  public function redirect(string $url): self
  {
    $this->setHeader('Location', URL_PATH . $url);
    $this->setContent('');  // No es necesario un contenido para redirecciones
    return $this;
  }

  public function json($data, $statusCode = 200): self
  {
    $this->setContent(json_encode($data));
    $this->setStatusCode($statusCode);
    $this->setHeader('Content-Type', 'application/json');
    return $this;
  }

  public function view(string $path, array $parameters = [], string $layout = '', string $screen = ''): self
  {
    $aditionParams = [];

    $content = $this->getViewContent($path, array_merge(['parameter' => $parameters], $aditionParams));

    // Si se especifica un layout, envuelve el contenido en ese layout.
    if ($layout) {
      $content = $this->getViewContent("layouts/$layout", array_merge(['content' => $content, 'parameter' => $parameters], $aditionParams));
    }

    $this->setContent($content);
    return $this;
  }

  public function getViewContent(string $path, array $parameters = []): string
  {
    $viewPath = __DIR__ . '/../../resources/Views/' . $path . '.view.php';

    // Comprobar si la vista existe.
    if (!file_exists($viewPath)) {
      throw new \Exception("View [{$path}] not found.");
    }

    // Utiliza el buffering de salida para capturar la vista en una variable.
    ob_start();

    // Establecer un manejador de errores personalizado.
    set_error_handler(function ($severity, $message, $file, $line) {
      throw new \ErrorException($message, 0, $severity, $file, $line);
    });

    try {
      extract($parameters, EXTR_SKIP);
      require $viewPath;
    } catch (\Throwable $e) {
      // Finalizar el buffering de salida y restaurar el manejador de errores original.
      ob_end_clean();
      restore_error_handler();
      // Relanzar la excepción.
      throw $e;
    }

    restore_error_handler();
    return ob_get_clean();
  }
}
