<?php

$routes = glob(__DIR__ . '/../Modules/*/*_routes.php');

$app->group('/api', function () use ($routes, $app, $container) {
    foreach ($routes as $route) {
        require $route;
    }
});
