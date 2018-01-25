<?php

namespace Tests\Helper;

use App\Base\AppContainer;
use App\Base\Helper\Jwt;
use Slim\Http\Request;
use Tests\Base\BaseApiCase;

class JwtTest extends BaseApiCase
{
    private $user;

    private $jwtTokenExample;

    protected function setUp()
    {
        parent::setUp();

        $this->user = [
            'email'    => 'testemail@testemail.com',
            'password' => 'testpassword',
        ];

        $this->jwtTokenExample = [
            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJleHAiOjE1MTY4MDE5MzksIm5iZiI6MTUxNjc5OTQzOSwiZGF0YSI6eyJfaWQiOiI1YTY4MzM3N2U3OTlmNzc2ZTIwZWM1MTQiLCJlbWFpbCI6ImFianNoYXNqQGpoaGFoc2dkLmNvbSIsInBhc3N3b3JkIjoiJDJ5JDEwJDlGOUlkUVY4dXk3bjgxTUdaOTVsYS5DYVJ3Uk1DOWh6WndHVWl0NVFIZHFTd2pmRG9KajZPIiwiY3JlYXRlZF9hdCI6IjE1MTY3NzgzNTk3NTgiLCJ1cGRhdGVfYXQiOiIxNTE2Nzc4MzU5ODI2In19.tW33h7TNhBdN8OnmHBTc5u7Xp7OXtJmx-e26qlw15M5eUQ_zkGZAULjkquGwLybmCOnI5LPi2IX23hjxnbiOIA',
        ];
    }

    public function testGenerateTokenReturnsAToken()
    {
        $jwtToken = Jwt::generateToken($this->user);

        $this->assertArrayHasKey('token', $jwtToken);
    }

    public function testDecodedJwtTokenHaveSameUserData()
    {
        $jwtToken = Jwt::generateToken($this->user);
        $jwtData  = Jwt::decodeJwtToken($jwtToken['token'])->data;

        $this->assertEquals($this->user['email'], $jwtData->email);
        $this->assertEquals($this->user['password'], $jwtData->password);
    }

    public function testFetchTokenMustReturnTheTokenFromRequestObject()
    {
        $requestMock = $this->createMock(Request::class);

        $requestMock->method('getQueryParam')->willReturn($this->jwtTokenExample);

        $fetchedToken = Jwt::fetchToken($requestMock);

        $this->assertEquals($this->jwtTokenExample, $fetchedToken);
    }

    public function testGetSecretAndConfigSecretAreSame()
    {
        $secretFromJwt    = Jwt::getSecret();
        $secretFromConfig = AppContainer::config('jwt')['secret'];

        $this->assertEquals($secretFromConfig, $secretFromJwt);
    }

    public function testGetExpiresInSecondsAndConfigExpiresInSecondsAreSame()
    {
        $expireFromJwt    = Jwt::getExpiresInSeconds();
        $expireFromConfig = AppContainer::config('jwt')['expire'];

        $this->assertEquals($expireFromConfig, $expireFromJwt);
    }

    public function testGetLeewayAndConfigLeewayAreSame()
    {
        $leewayFromJwt    = Jwt::getLeeway();
        $leewayFromConfig = AppContainer::config('jwt')['leeway'];

        $this->assertEquals($leewayFromConfig, $leewayFromJwt);
    }

    public function testGetAlgorithmAndConfigAlgorithmAreSame()
    {
        $algorithmFromJwt    = Jwt::getAlgorithm();
        $algorithmFromConfig = AppContainer::config('jwt')['algorithm'];

        $this->assertEquals($algorithmFromConfig, $algorithmFromJwt);
    }
}