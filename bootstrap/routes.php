<?php

$routes = glob(__DIR__ . '/../Module/*/*_routes.php');

$app->group('/api', function () use ($routes, $app, $container) {
    foreach ($routes as $route) {
        require $route;
    }
});
