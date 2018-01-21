<?php
// Set default timezone
date_default_timezone_set('UTC');

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../app/tests/bootstrap/settings.php';

$app = \App\AppContainer::getAppInstance($settings);

$container = $app->getContainer();

/** @var \Slim\Http\Request $request */
$request = $container['request'];
/** @var \Slim\Http\Uri $uri */
$uri = $request->getUri();

define('__ROOTURL__', $request->getServerParam('HTTP_REFERER'));
define('__ROOTURI__', $uri->getBasePath());
define('__PATH__', $uri->getPath());
define('__APPDIR__', __DIR__);

define('__ISAPI__', (bool)stristr($uri->getPath(), '/api/'));

require __DIR__ . '/../app/bootstrap/dep.base.php';
require __DIR__ . '/../app/bootstrap/middlewares.php';
require __DIR__ . '/../app/bootstrap/routes.php';