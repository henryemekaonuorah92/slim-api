<?php

namespace Tests\Base;


use App\Base\AppContainer;
use Slim\App;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;

class BaseAppCase extends \PHPUnit\Framework\TestCase
{
    /** @var App */
    public static $appInstance;
    /** @var Container */
    public static $containerInstance;

    /** @var Response */
    public $response;

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

    /**
     * @param $method
     * @param $uir
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function request($method, $uir, $options = array())
    {
        $request = $this->prepareRequest($method, $uir, $options);

        $response = new Response();
        $app = static::$appInstance;
        $this->response = $rs = $app($request, $response);
        return $rs;
    }


    /**
     * @param $expectedStatus
     */
    protected function assertThatResponseHasStatus($expectedStatus)
    {
        $this->assertEquals($expectedStatus, $this->response->getStatusCode());
    }

    protected function assertThatResponseHasContentType($expectedContentType)
    {
        $this->assertContains($expectedContentType, $this->response->getHeader('Content-Type'));
    }

    protected function responseData()
    {
        return json_decode((string)$this->response->getBody(), true);
    }

}
