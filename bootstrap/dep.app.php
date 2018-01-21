<?php
/**
 * @param \Slim\Container $container
 * @return mixed
 */
$container['mongodb'] = function (\Slim\Container $container) {
    $config = \Core\AppContainer::config('mongodb');

    $connection = new \Util\Db\MongoManager($container);
    $connection->addConnection([
        'uri' => $config['uri'],
        'database' => $config['database'],
        'uriOptions' => $config['uriOptions'],
        'driverOptions' => $config['driverOptions'],
    ]);

    return $connection->getConnection();
};

/**
 * @return \Util\Helpers\Mailer
 */
$container['mailer'] = function () {
    $config = \Core\AppContainer::config('mailer');

    return \Util\Helpers\Mailer::fromArray([
        'host' => $config['SMTP_HOST'],
        'port' => $config['SMTP_PORT'],
        'encryption' => $config['SMTP_ENCRYPTION'],
        'username' => $config['SMTP_USERNAME'],
        'password' => $config['SMTP_PASSWORD'],
        'name' => $config['SMTP_NAME'],
    ]);
};
