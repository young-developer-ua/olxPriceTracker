<?php

use FastRoute\RouteCollector;

require_once 'vendor/autoload.php';
require_once 'routes/web.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    $routeDefinitionCallback = require 'routes/web.php';
    $routeDefinitionCallback($r);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        require 'public/404.html';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        echo "Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        [$controller, $method] = $routeInfo[1];
        $controllerInstance = new $controller();
        $controllerInstance->$method();
        break;
}
