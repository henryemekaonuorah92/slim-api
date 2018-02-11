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
            ['email' => 'email@', 'password' => '']
        );

        $rs = $this->responseDataArr();
        $this->assertContains('Email is not a valid', $rs['message']);
        $this->assertContains('Password is required', $rs['message']);
        $this->assertContains('Password must be at least', $rs['message']);

        $emailRnd = rand().'email@gmail.com';
        $response = $this->sendHttpRequest(
            'POST', '/api/user/register',
            ['email' => $emailRnd, 'password' => 'Hello world']
        );

        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains($emailRnd, $rs['email']);

        $this->sendHttpRequest(
            'POST', '/api/user/login',
            ['email' => $emailRnd, 'password' => 'Hello world']
        );

        $this->assertThatResponseHasStatus(200);
        $rs = $this->responseDataArr();
        $this->assertArrayHasKey('token', $rs);

    }

}