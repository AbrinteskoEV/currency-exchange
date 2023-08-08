<?php

declare(strict_types=1);

/** @var Router $router */

use Laravel\Lumen\Routing\Router;

$router->get('', function () use ($router) {
    return $router->app->version();
});
