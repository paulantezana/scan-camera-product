<?php

// Load helper to views
require_once(__DIR__ . '/../app/helpers.php');

// Load constants
require_once(__DIR__ . '/../config/constants.php');

// Load routes
require_once(__DIR__ . '/../routes/web.php');


try {
  App\Core\Router::resolve();
} catch (Exception $e) {
  App\Exceptions\Handlers\ExceptionHandler::handle($e);
}

