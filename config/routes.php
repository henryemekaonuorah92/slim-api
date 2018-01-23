<?php

$routes = glob(__DIR__ . '/../app/*/*api_routes.php');

$app->group('/api', function () use ($routes, $app, $container) {
    foreach ($routes as $route) {
        require $route;
    }
});

// require extra routes -- ignored file to extend your app
$extraRoutesFile = __DIR__ . '/ext.routes.php';
if (file_exists($extraRoutesFile)) {
    require_once $extraRoutesFile;
}
