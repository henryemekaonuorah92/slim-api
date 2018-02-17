<?php
return [
    'settings' => [
        // jwt config
        'jwt' => [
            'enabled' => true,
            'secret' => 'qwertyuiopasdfghjklzxcvbnm123456',
            'expire' => 2500,
            'leeway' => 60,
            'algorithm' => 'HS512',
            'query' => 'x-token',
            'header' => 'x-token',
            'cookie' => 'x-token',
            'bypass' => [
                '/api/account/user/register',
                '/api/account/user/login',
                '/api/ping',
            ]
        ],
        // mongodb configuration @link
        'mongodb' => [
            'uri' => 'mongodb://localhost:27017',
            'database' => 'phonebook_test',
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
            'path' => __DIR__ . '/../../storage/app_test.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ]
];
