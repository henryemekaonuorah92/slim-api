<?php

$baseSetting = [
    'settings' => [
        // jwt config
        'jwt' => [
            'enabled' => true,
            'secret' => 'qwertyuiopasdfghjklzxcvbnm123456',
            'expire' => 2500,
            'leeway' => 60,
            'algorithm' => 'HS512',
            'header' => 'authorization',
            'query' => 'token',
            'bypass' => [
                'POST /api/my-account/user/register',
                'POST /api/my-account/user/login',
                'GET /api/ping',
            ]
        ],
        // mongodb configuration @link
        'mongodb.default' => [
            'uri' => 'mongodb://localhost:27017',
            'database' => 'db',
            'uriOptions' => [],
            'driverOptions' => []
        ],

        'determineRouteBeforeAppMiddleware' => true, // Only set this if you need access to route within middleware
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        //Monolog settings
        'logger' => [
            'err_trace' => 1, // allow trace in errors set to 0 in production
            'err_msg' => 1, // show error messages from exception in errors set to 0 in production
            'name' => 'app',
            'path' => __DIR__ . '/../storage/logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ]
];

// require extra settings -- ignored file to extend your app
$extraSetting = [];
$extraSettingFile = __DIR__ . '/ext.settings.php';
if (file_exists($extraSettingFile)) {
    require_once $extraSettingFile;
}

return array_replace_recursive($baseSetting, $extraSetting);
