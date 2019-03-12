<?php

namespace App\Base;

use Monolog\Logger;
use Slim\App;
use Slim\Container;

class AppContainer
{
    /** @var App */
    private static $app = [];

    /**
     * @param array $settings
     * @param int   $appId
     *
     * @return mixed
     */
    public static function getAppInstance($settings = [], $appId = 0)
    {
        if (!isset(self::$app[$appId]) || null === self::$app[$appId]) {
            self::$app[$appId] = self::makeAppInstance($settings);
        }

        return self::$app[$appId];
    }

    /**
     * @param int $appId
     *
     * @return \Psr\Container\ContainerInterface|Container
     */
    public static function getContainer($appId = 0)
    {
        return static::getAppInstance([], $appId)->getContainer();
    }


    /**
     * @param int $appId
     *
     * @return Logger
     */
    public static function getLogger($appId = 0)
    {
        return static::getContainer($appId)->get('logger');
    }


    /**
     * @param      $key
     * @param null $defaultValue
     * @param int  $appId
     *
     * @return null|mixed
     */
    public static function config($key, $defaultValue = null, $appId = 0)
    {
        $settings = static::getContainer($appId)->get('settings');
        return $settings[$key] ?? $defaultValue;
    }

    /**
     * @param     $key
     * @param     $value
     * @param int $appId
     *
     * @return \Psr\Container\ContainerInterface|Container
     */
    public static function setConfig($key, $value, $appId = 0)
    {
        $container = static::getContainer($appId);
        $container->get('settings')->replace([
            $key => $value,
        ]);
        return $container;
    }

    /**
     * @param array $settings
     *
     * @return App
     */
    private static function makeAppInstance($settings = [])
    {
        $app = new App($settings);
        // do all logic for adding routes etc
        return $app;
    }
}
