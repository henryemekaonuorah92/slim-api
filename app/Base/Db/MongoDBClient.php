<?php

namespace App\Base\Db;

use MongoDB\Client;
use Psr\Container\ContainerInterface;

class MongoDBClient
{
    protected $config = [];
    protected $container = [];
    protected static $instances = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Register a connection with the manager.
     * @param array $config
     * @param string $name
     * @return $this
     */
    public function addConnection(array $config, $name = 'mongodb.default')
    {
        $configKey = $name . '__config';
        $this->container[$configKey] = function () use ($config) {
            return $config;
        };
        return $this;
    }

    /**
     * @param string $name
     * @return Client
     */
    public function getConnection($name = 'mongodb.default')
    {
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }
        $configKey = $name . '__config';
        $config = $this->container[$configKey];

        $driverOptions = $config['driverOptions'] ?? [];
        $driverOptions['typeMap'] = [
            'root' => \App\Base\Model\Types\MDoc::class,
            'array' => \App\Base\Model\Types\MDoc::class,
            'document' => \App\Base\Model\Types\MDoc::class,
        ];

        $client = new Client($config['uri'], $config['uriOptions'], $driverOptions);
        self::$instances[$name] = $client;

        return self::$instances[$name];
    }
}