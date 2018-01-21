<?php
/**
 * @param \Slim\Container $container
 * @return mixed
 */
$container['mongodb'] = function (\Slim\Container $container) {
    $config = \App\Core\AppContainer::config('mongodb');

    $connection = new \App\Util\Db\MongoManager($container);
    $connection->addConnection([
        'uri' => $config['uri'],
        'database' => $config['database'],
        'uriOptions' => $config['uriOptions'],
        'driverOptions' => $config['driverOptions'],
    ]);

    return $connection->getConnection();
};