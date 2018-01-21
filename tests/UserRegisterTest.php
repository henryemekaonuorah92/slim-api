<?php

namespace Tests;

use Slim\Http\Response;
use Tests\Base\BaseAppCase;

class UserRegisterTest extends BaseAppCase
{

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
        $rs = $app($request, $response);
        return $rs;
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testRegisterUser()
    {
        $response = $this->request('POST', '/api/user/register', ['email' => 'email@gmail.com', 'password' => 'Hello world']);
        $this->assertSame($response->getStatusCode(), 200);
        $this->assertSame((string)$response->getBody(), '{"n":1}');

    }

//    public function testTodoGet()
//    {
//        $env = Environment::mock([
//            'REQUEST_METHOD' => 'GET',
//            'REQUEST_URI' => '/',
//        ]);
//        $req = Request::createFromEnvironment($env);
//        static::getContainerInstance()['request'] = $req;
//
//        $response = static::getAppInstance()->run(true);
//        $this->assertSame($response->getStatusCode(), 200);
//        $this->assertSame((string)$response->getBody(), "Hello, Todo");
//    }

//    public function testTodoGetAll()
//    {
//        $env = Environment::mock([
//            'REQUEST_METHOD' => 'GET',
//            'REQUEST_URI' => '/todo',
//        ]);
//        $req = Request::createFromEnvironment($env);
//        $this->app->getContainer()['request'] = $req;
//        $response = $this->app->run(true);
//        $this->assertSame($response->getStatusCode(), 200);
//        $result = json_decode($response->getBody(), true);
//        $this->assertSame($result["message"], "Hello, Todo");
//    }
//
//    public function testTodoPost()
//    {
//        $id = 1;
//        $env = Environment::mock([
//            'REQUEST_METHOD' => 'POST',
//            'REQUEST_URI' => '/todo/' . $id,
//            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
//        ]);
//        $req = Request::createFromEnvironment($env)->withParsedBody([]);
//        $this->app->getContainer()['request'] = $req;
//        $response = $this->app->run(true);
//        $this->assertSame($response->getStatusCode(), 200);
//        $result = json_decode($response->getBody(), true);
//        $this->assertSame($result["message"], "Todo " . $id . " updated successfully");
//    }
//
//    public function testTodoDelete()
//    {
//        $id = 1;
//        $env = Environment::mock([
//            'REQUEST_METHOD' => 'DELETE',
//            'REQUEST_URI' => '/todo/' . $id,
//            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
//        ]);
//        $req = Request::createFromEnvironment($env)->withParsedBody([]);
//        $this->app->getContainer()['request'] = $req;
//        $response = $this->app->run(true);
//        $this->assertSame($response->getStatusCode(), 200);
//        $result = json_decode($response->getBody(), true);
//        $this->assertSame($result["message"], "Todo " . $id . " deleted successfully");
//    }
}