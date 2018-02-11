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

class BaseApiCase extends BaseCase
{
    /** @var App */
    public static $appInstance;
    /** @var Container */
    public static $containerInstance;

    /** @var Response */
    public $response;

    /** @var array */
    public static $jwtData = '';

    /**
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public static function setUpBeforeClass()
    {
        static::$appInstance = AppContainer::getAppInstance();
        static::$containerInstance = AppContainer::getContainer();
        static::$jwtData = static::ReqJwtToken();
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
        $mock = [
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => $url,
            'REQUEST_METHOD' => $method,
        ];
        if (!empty(static::$jwtData['token'])) {
            $mock['HTTP_AUTHORIZATION'] = 'bearer ' . static::$jwtData['token'];
        }
        $env = Environment::mock($mock);

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
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function sendHttpRequest($method, $uir, $options = array())
    {
        $request = $this->prepareRequest($method, $uir, $options);

        $response = new Response();
        unset(static::$containerInstance['request']);
        static::$containerInstance['request'] = $request;
        $app = static::$appInstance;
        $this->response = $app->run(true);
        return $this->response;
    }

    /**
     * @param $expectedStatus
     */
    public function assertThatResponseHasStatus($expectedStatus)
    {
        $this->assertEquals($expectedStatus, $this->response->getStatusCode());
    }

    /**
     * @param $expectedContentType
     */
    public function assertThatResponseHasContentType($expectedContentType)
    {
        $this->assertContains($expectedContentType, $this->response->getHeader('Content-Type'));
    }

    /**
     * @return mixed
     */
    public function responseDataArr()
    {
        return json_decode((string)$this->response->getBody(), true);
    }


    /**
     * @param array $userData
     * @return array
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public static function ReqJwtToken($userData = [])
    {
        $token = '';
        $user = [];
        $instance = new static();

        $userData['email'] = $userData['email'] ?? 'testemail@testemail.com';
        $userData['password'] = $userData['password'] ?? 'testpassword';

        $response = $instance->sendHttpRequest(
            'POST', '/api/my-account/user/login',
            $userData
        );

        if ($response->getStatusCode() != 200) {
            $response = $instance->sendHttpRequest(
                'POST', '/api/my-account/user/register',
                $userData
            );
            if ($response->getStatusCode() == 200) {
                $response = $instance->sendHttpRequest(
                    'POST', '/api/my-account/user/login',
                    $userData
                );
            } else {
                $instance->fail('cannot register user ' . $response->getStatusCode());
            }
        }

        $dataArr = $instance->responseDataArr();
        $token = $dataArr['token'] ?? '';
        $user = $userData;
        return ['token' => $token, 'user' => $user];
    }
}
