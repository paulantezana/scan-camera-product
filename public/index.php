<?php

// Habilita la generación de mensajes de error
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

// Esta función manejará los errores y los escribirá en el archivo de registro
function customErrorHandler($errno, $errstr, $errfile, $errline)
{
  // Formatea el mensaje de error
  $errorMessage = date('Y-m-d H:i:s') . " - [{$errno}] {$errstr} en {$errfile} línea {$errline}\n";

  // Escribe el mensaje de error en el archivo de registro
  error_log($errorMessage, 3, __DIR__ . '/../error.log');
}

// Establece la función de gestión de errores personalizada
set_error_handler('customErrorHandler');

// Start Time Zone
date_default_timezone_set('America/Lima');

// Start Microtime
define('APP_START', microtime(true));

// server should keep session data for AT LEAST 2 hour
ini_set('session.gc_maxlifetime', 7200);

// each client should remember their session id for EXACTLY 2 hour
session_set_cookie_params(7200);

// Sesion Start
session_start();

// Autoload
require_once(__DIR__ . '/../vendor/autoload.php');

// Init app
require_once(__DIR__ . '/../bootstrap/app.php');


// Este son cambios en los archivos test
