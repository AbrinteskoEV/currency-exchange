<?php

declare(strict_types=1);

/** @var Router $router */

use Application\Http\Controllers\MarketController;
use Application\Http\Middleware\MainMiddleware;
use Laravel\Lumen\Routing\Router;

$router->get('', function () use ($router) {
    return $router->app->version();
});


$router->group(['middleware' => MainMiddleware::class], function () use ($router) {
    $router->group(['prefix' => 'v1'], function () use ($router) {
        $router->group(['prefix' => 'market'], function () use ($router) {
            $router->group(['prefix' => 'price'], function () use ($router) {
                $router->get('', MarketController::class . '@getPairPrice');
                $router->get('min', MarketController::class . '@getMinPairPrice');
                $router->get('max', MarketController::class . '@getMaxPairPrice');

                $router->group(['prefix' => 'many'], function () use ($router) {
                    $router->get('', MarketController::class . '@getManyPairPrice');
                    $router->get('min', MarketController::class . '@getManyMinPairPrice');
                    $router->get('max', MarketController::class . '@getManyMaxPairPrice');
                });
            });
        });
    });
});
