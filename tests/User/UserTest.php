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
        $response = $this->sendHttpRequest(
            'POST', '/api/user/register',
            ['email' => 'email@gmail.com', 'password' => 'Hello world']
        );

        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains('email@gmail.com', $rs['email']);


    }

    /**
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testLoginUser()
    {
        $this->sendHttpRequest(
            'POST', '/api/user/login',
            ['email' => 'email@gmail.com', 'password' => 'Hello world']
        );

        $this->assertThatResponseHasStatus(200);
        $rs = $this->responseDataArr();
        $this->assertArrayHasKey('token', $rs);
    }

}