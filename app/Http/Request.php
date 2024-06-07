<?php

namespace App\Http;

class Request
{
  private array $data;
  private array $server;

  public function __construct(array $requestData = [], array $serverData = [])
  {
    $this->data = $requestData ?: $_REQUEST;
    $this->server = $serverData ?: $_SERVER;
  }

  public function input(string $key, $default = null)
  {
    return $this->data[$key] ?? $default;
  }

  public function getClientIp(): string
  {
    return '';
  }

  public function getMethod(): string
  {
    return $this->server['REQUEST_METHOD'] ?? 'GET';
  }

  public function getUri(): string
  {
    return $this->server['REQUEST_URI'] ?? '/';
  }

  public function getUrl(): string
  {
    return $this->getScheme() . '://' . $this->getHost() . $this->getUri();
  }

  public function getScheme(): string
  {
    return isset($this->server['HTTPS']) && $this->server['HTTPS'] === 'on' ? 'https' : 'http';
  }

  public function getHost(): string
  {
    return $this->server['HTTP_HOST'] ?? '';
  }

  public function getPort(): int
  {
    return (int) ($this->server['SERVER_PORT'] ?? 80);
  }

  public function getHeader(string $header): ?string
  {
    $key = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
    return $this->server[$key] ?? null;
  }

  public function getBody(): array
  {
    if ($this->expectsJson()) {
      $bodyData = file_get_contents('php://input');
      $body = json_decode($bodyData, true);
    } else {
      $body = $_POST;
    }
    return $body;
  }

  public function getQueryParams()
  {
    return $_GET;
  }

  public function getBasePath(): string
  {
    return URL;
  }

  public function expectsJson(): bool
  {
    if ((strtolower($this->server['HTTP_ACCEPT'] ?? '') === 'application/json') || (strtolower($this->server['CONTENT_TYPE'] ?? '') === 'application/json')) {
      return true;
    }
    return false;
  }
}
