<?php

namespace App\Base\Db;

use MongoDB\Client;
use Psr\Container\ContainerInterface;

class MongoDBClient
{
    const MONGO_DI = 'mongodb';
    const MONGO_CONFIG_CONNECTION = 'mongo.database.connections';
    protected $config = [];
    protected $container = [];
    protected static $instances = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Register a connection with the manager.
     *
     * @param  array $config
     * @param  string $name
     * @return void
     */
    public function addConnection(array $config, $name = 'default')
    {
        $connections = $this->container[self::MONGO_CONFIG_CONNECTION] ?? [];

        $connections[$name] = $config;

        $this->container[self::MONGO_CONFIG_CONNECTION] = function () use ($connections) {
            return $connections;
        };
    }

    public function getConnection($name = 'default')
    {
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }
        $config = $this->container[self::MONGO_CONFIG_CONNECTION][$name];
        $driverOptions = $config['driverOptions'] ?? [];
        $driverOptions['typeMap'] = [
            'root' => \App\Base\Model\Types\MDoc::class,
            'array' => \App\Base\Model\Types\MDoc::class,
            'document' => \App\Base\Model\Types\MDoc::class,
//            'array' => 'MongoDB\Model\BSONArray',
//            'document' => 'MongoDB\Model\BSONDocument',
        ];
        $client = new Client($config['uri'], $config['uriOptions'], $driverOptions);
        self::$instances[$name] = $client;

        return self::$instances[$name];

    }
}