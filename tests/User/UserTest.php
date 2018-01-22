<?php

namespace Tests\User;

use Tests\Base\BaseApiCase;

class UserTest extends BaseApiCase
{

    /**
     * @throws \Exception
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

    /**
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testLoginUser()
    {
        $this->request(
            'POST', '/api/user/login',
            ['email' => 'email@gmail.com', 'password' => 'Hello world']
        );

        $this->assertThatResponseHasStatus(200);
        $rs = $this->responseData();
        $this->assertArrayHasKey('token', $rs);
    }

    /**
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
//    public function testUpdateUser()
//    {
//        $this->request(
//            'POST', '/api/user/login',
//            ['email' => 'email@gmail.com', 'password' => 'Hello world']
//        );
//
//        $this->assertThatResponseHasStatus(200);
//        $this->assertArrayHasKey('token', $this->responseData());
//    }


}