<?php

return [
    'settings' => [
        // jwt config
        'jwt' => [
            'enabled' => false,
            'secret' => 'qwertyuiopasdfghjklzxcvbnm123456',
            'expire' => 2500,
            'leeway' => 60,
            'algorithm' => 'HS512',
            'query' => 'x-token',
            'header' => 'x-token',
            'cookie' => 'x-token',
            'bypass' => [
                '/api/user/register',
                '/api/user/login',
                '/api/ping',
            ]
        ],
        // mongodb configuration @link
        'mongodb' => [
            'uri' => 'mongodb://localhost:27017',
            'database' => 'phonebook',
            'uriOptions' => [],
            'driverOptions' => []
        ],

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
