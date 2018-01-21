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
        $response = $this->request(
            'POST', '/api/user/register',
            ['email' => 'email@gmail.com', 'password' => 'Hello world']
        );

        $this->assertSame($response->getStatusCode(), 200);
        $this->assertSame((string)$response->getBody(), '{"n":1}');

    }
}