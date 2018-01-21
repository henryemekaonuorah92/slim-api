<?php
//date_default_timezone_set('UTC');
//if (function_exists('xdebug_disable')) {
//    xdebug_disable();
//}

// Set default timezone

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../bootstrap/settings.php';

$app = \App\Core\AppContainer::getAppInstance($settings);

$container = $app->getContainer();

/** @var \Slim\Http\Request $request */
$request = $container['request'];
/** @var \Slim\Http\Uri $uri */
$uri = $request->getUri();

define('__ROOTURL__', $request->getServerParam('HTTP_REFERER'));
define('__ROOTURI__', $uri->getBasePath());
define('__PATH__', $uri->getPath());
define('__APPDIR__', __DIR__);

require __DIR__ . '/../bootstrap/dep.base.php';
require __DIR__ . '/../bootstrap/mw.base.php';
require __DIR__ . '/../bootstrap/routes.php';

try {
    $app->run();
} catch (\Exception $e) {
}
