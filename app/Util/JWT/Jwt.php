<?php

declare(strict_types=1);

namespace App\Util\JWT;

use App\Core\AppContainer;
use Psr\Http\Message\RequestInterface;

class Jwt
{
    /**
     * @return null|string
     */
    public static function getSecret()
    {
        $jwtConfig = AppContainer::config('jwt');
        return $jwtConfig['secret'] ?? null;
    }

    /**
     * @return int
     */
    public static function getExpiresInSeconds()
    {
        $jwtConfig = AppContainer::config('jwt');
        return $jwtConfig['expire'] ?? 0;
    }

    /**
     * @return int
     */
    public static function getLeeway()
    {
        $jwtConfig = AppContainer::config('jwt');
        return $jwtConfig['leeway'] ?? 0;
    }

    /**
     * @return int
     */
    public static function getAlgorithm()
    {
        $jwtConfig = AppContainer::config('jwt');
        return $jwtConfig['algorithm'] ?? '';
    }

    public static function decodeJwtToken($token, $secret = null, $leeway = null, $algorithm = null)
    {
        $secret = $secret ?? static::getSecret();
        $leeway = $leeway ?? static::getLeeway();
        $algorithm = $algorithm ?? static::getAlgorithm();

        \Firebase\JWT\JWT::$leeway = $leeway;
        return \Firebase\JWT\JWT::decode($token, $secret, [$algorithm]);
    }

    /**
     * @param $data
     * @param null $expirySeconds
     * @param null $algorithm
     * @return array
     */
    public static function generateToken($data, $expirySeconds = null, $algorithm = null)
    {
        $expirySeconds = $expirySeconds ?? static::getExpiresInSeconds();
        $secret = $secret ?? static::getSecret();
        $algorithm = $algorithm ?? static::getAlgorithm();

        $now = time();
        $exp = $now + $expirySeconds;
        $payload = [
            "exp" => $exp,
            "nbf" => $now,
            "data" => $data,
        ];
        $rs = [
            'token' => \Firebase\JWT\JWT::encode($payload, $secret, $algorithm),
            'expires' => $exp,
        ];
        return $rs;
    }

    /**
     * todo fix
     * @param RequestInterface $request
     * @return mixed
     */
    public static function fetchToken(RequestInterface $request)
    {
        $jwtConfig = AppContainer::config('jwt');
        $queryParam = $jwtConfig['query'] ?? false;
        $headerName = $jwtConfig['header'] ?? false;
        $cookieName = $jwtConfig['cookie'] ?? false;
        $token = null;

        switch (true) {
            case $queryParam:
                $token = $request->getQueryParam($queryParam);
            //break;
            case $headerName:
                if (!$token) {
                    $headrVal = $request->getHeader('Authorization')[0] ?? '';
                    $tokenArr = explode(' ', $headrVal);
                    $token = $tokenArr[1] ?? null;
                }
            //break;
            case $cookieName:
                if (!$token) {
                    $token = $request->getQueryParam($queryParam);
                }
            //break;
            default:
                break;
        }

        return $token;
    }


}
