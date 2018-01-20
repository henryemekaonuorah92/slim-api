<?php

namespace App;

use Slim\App;
use Slim\Container;

class AppContainer
{
    /** @var App */
    private static $app = null;

    /**
     * @param array $settings
     * @return App|null
     */
    public static function getAppInstance($settings = [])
    {
        if (null === self::$app) {
            self::$app = self::makeAppInstance($settings);
        }

        return self::$app;
    }

    /**
     * @return \Psr\Container\ContainerInterface|Container
     */
    public static function getContainer()
    {
        return static::getAppInstance()->getContainer();
    }

    /**
     * @param $key
     * @param null $defaultValue
     * @return mixed|null
     */
    public static function config($key, $defaultValue = null)
    {
        $settings = static::getContainer()->get('settings');
        return $settings[$key] ?? $defaultValue;
    }

    /**
     * @param $key
     * @param $value
     * @return \Psr\Container\ContainerInterface|Container
     */
    public static function setConfig($key, $value)
    {
        $container = static::getContainer();
        $container->get('settings')->replace([
            $key => $value,
        ]);
        return $container;
    }

    /**
     * @param array $settings
     * @return App
     */
    private static function makeAppInstance($settings = [])
    {
        $app = new App($settings);
        // do all logic for adding routes etc
        return $app;
    }
}
