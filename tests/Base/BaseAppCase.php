<?php

namespace Tests\Base;


use App\AppContainer;
use Slim\App;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Uri;

class BaseAppCase extends \PHPUnit\Framework\TestCase
{
    /** @var App */
    public static $appInstance;
    /** @var Container */
    public static $containerInstance;

    /**
     *
     */
    public static function setUpBeforeClass()
    {
        static::$appInstance = AppContainer::getAppInstance();
        static::$containerInstance = AppContainer::getContainer();
        parent::setUpBeforeClass();
    }

    /**
     * @return App
     */
    public function getAppInstance()
    {
        return static::$appInstance;
    }

    /**
     * @return Container
     */
    public function getContainerInstance()
    {
        return static::$containerInstance;
    }

    /**
     * @param $method
     * @param $url
     * @param array $requestParameters
     * @return Request
     */
    public function prepareRequest($method, $url, array $requestParameters)
    {
        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => $url,
            'REQUEST_METHOD' => $method,
        ]);

        $parts = explode('?', $url);

        if (isset($parts[1])) {
            $env['QUERY_STRING'] = $parts[1];
        }

        $uri = Uri::createFromEnvironment($env);
        $headers = Headers::createFromEnvironment($env);
        $cookies = [];

        $serverParams = $env->all();

        $body = new RequestBody();
        $body->write(json_encode($requestParameters));

        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body);

        return $request->withHeader('Content-Type', 'application/json');
    }


}
