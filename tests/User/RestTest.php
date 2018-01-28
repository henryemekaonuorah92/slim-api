<?php

namespace Tests\User;

use App\User\UserModel;
use Tests\Base\BaseApiCase;

class RestTest extends BaseApiCase
{
    /**
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testUserFlow()
    {
        $userEmail = rand(0, 1000000) . 'useremail@email.com';
        $userPass = rand(0, 1000000) . 'usertestpass';
        $idFieldName = (new UserModel())->getIdFieldName();
        // insert user
        $response = $this->sendHttpRequest(
            'POST', '/api/user/register',
            ['email' => $userEmail, 'password' => $userPass]
        );

        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains($userEmail, $rs['email']);
        $userData = $rs;
        $userId = $userData[$idFieldName];

        // login wrong email
        $response = $this->sendHttpRequest(
            'POST', '/api/user/login',
            ['email' => $userEmail . 'wrong', 'password' => $userPass]
        );
        $this->assertSame($response->getStatusCode(), 400);
        $rs = $this->responseDataArr();
        $this->assertContains('not found', $rs['message']);

        // login wrong password
        $response = $this->sendHttpRequest(
            'POST', '/api/user/login',
            ['email' => $userEmail, 'password' => $userPass . 'wrong']
        );
        $this->assertSame($response->getStatusCode(), 400);
        $rs = $this->responseDataArr();
        $this->assertContains('password is incorrect', $rs['message']);


        // login and save token to assign it for all next requests
        $response = $this->sendHttpRequest(
            'POST', '/api/user/login',
            ['email' => $userEmail, 'password' => $userPass]
        );

        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $userToken = $rs['token'];

        $origJWTData = self::$jwtData;
        self::$jwtData = ['token' => $userToken, 'user' => $userData];


        // get user
        $response = $this->sendHttpRequest(
            'GET', '/api/user/' . $userId
        );

        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains($userId, $rs[$idFieldName]);

        // update user
        $response = $this->sendHttpRequest(
            'PUT', '/api/user/' . $userId,
            ['email' => $userEmail, 'password' => $userPass . 'update']
        );
        $this->assertSame($response->getStatusCode(), 200);

        // get user
        $response = $this->sendHttpRequest(
            'GET', '/api/user/' . $userId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains($userId, $rs[$idFieldName]);

        // update user
        $response = $this->sendHttpRequest(
            'DELETE', '/api/user/' . $userId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertEquals(1, $rs['ok']);

        // get user
        $response = $this->sendHttpRequest(
            'GET', '/api/user/' . $userId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertEquals(null, $rs);


        // get user exception
        $response = $this->sendHttpRequest(
            'GET', '/api/user/' . '12312'
        );
        $this->assertSame($response->getStatusCode(), 400);
        $rs = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $rs['message']);

        // update user exception
        $response = $this->sendHttpRequest(
            'PUT', '/api/user/' . '12312',
            ['email' => $userEmail]
        );
        $this->assertSame($response->getStatusCode(), 400);
        $rs = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $rs['message']);

        // test user/me with token
        $this->sendHttpRequest(
            'GET', '/api/user/me'
        );

        // todo conditional based on jwt setting if enabled
        $this->assertThatResponseHasStatus(200);
        $rs = $this->responseDataArr();
        $this->assertEquals($userEmail, $rs['email']);

        // reset token to origin base api user
        self::$jwtData = $origJWTData;

    }
}