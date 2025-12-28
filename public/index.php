<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

// ⚠️ On ne charge Dotenv qu'en dev
if (file_exists(dirname(__DIR__) . '/.env') && ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'prod') !== 'prod') {
    (new Symfony\Component\Dotenv\Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

$kernel = new Kernel($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'prod', ($_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? false));
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
