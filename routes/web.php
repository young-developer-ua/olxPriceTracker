<?php

require 'vendor/autoload.php';

use Src\Controllers\HomeController;
use FastRoute\RouteCollector;
use Src\Controllers\SubscriptionController;

return function (RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('POST', '/subscribe', [SubscriptionController::class, 'subscribe']);
};
