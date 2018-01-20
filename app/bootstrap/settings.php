<?php
return [
    'settings' => [

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
            'ERR_TRACE' => 1, // allow trace in errors set to 0 in production
            'ERR_MSG' => 0, // show error messages from exception in errors set to 0 in production
            'name' => 'app',
            'path' => __DIR__ . '/../_logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ]
];
