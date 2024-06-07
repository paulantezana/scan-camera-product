<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$requestUri = parse_url('http://example.com' . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
$virtualPath = '/' . ltrim(substr($requestUri, strlen($scriptName)), '/');
$hostName = (stripos(@$_SERVER['REQUEST_SCHEME'], 'https') === 0 ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];

define('HOST', $hostName);
define('PORT', strlen($_SERVER['SERVER_PORT']) > 0 ? ':' . $_SERVER['SERVER_PORT'] : '');
define('URI', $requestUri);
define('URL_PATH', rtrim($scriptName, '/'));
define('URL', $virtualPath);

define('ROOT_DIR', $_SERVER["DOCUMENT_ROOT"] . rtrim($scriptName, '/') . '/..');
define('PUBLIC_ROOT_DIR', $_SERVER["DOCUMENT_ROOT"] . rtrim($scriptName, '/'));

define('SESS_USER', 'df120');
define('APP_VERSION', '1.1.912');
