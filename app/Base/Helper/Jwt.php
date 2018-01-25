<?php


namespace App\Base\Helper;

use App\Base\AppContainer;
use Slim\Http\Request;

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
        $secret    = $secret ?? static::getSecret();
        $leeway    = $leeway ?? static::getLeeway();
        $algorithm = $algorithm ?? static::getAlgorithm();

        \Firebase\JWT\JWT::$leeway = $leeway;

        return \Firebase\JWT\JWT::decode($token, $secret, [$algorithm]);
    }

    /**
     * @param      $data
     * @param null $expirySeconds
     * @param null $algorithm
     *
     * @return array
     */
    public static function generateToken($data, $expirySeconds = null, $algorithm = null)
    {
        $expirySeconds = $expirySeconds ?? static::getExpiresInSeconds();
        $secret        = $secret ?? static::getSecret();
        $algorithm     = $algorithm ?? static::getAlgorithm();

        $now     = time();
        $exp     = $now + $expirySeconds;
        $payload = [
            "exp"  => $exp,
            "nbf"  => $now,
            "data" => $data,
        ];
        $rs      = [
            'token'   => \Firebase\JWT\JWT::encode($payload, $secret, $algorithm),
            'expires' => $exp,
        ];

        return $rs;
    }

    /**
     * @param Request $request
     *
     * @return mixed|null
     */
    public static function fetchToken(Request $request)
    {
        $jwtConfig  = AppContainer::config('jwt');
        $queryParam = $jwtConfig['query'] ?? '';
        $headerName = $jwtConfig['header'] ?? '';

        // fetch from query params
        $token = $request->getQueryParam($queryParam);


        // if not exist fetch from header
        if (!$token) {
            $headerVal = $request->getHeader($headerName)[0] ?? '';
            $tokenArr  = explode(' ', $headerVal);
            $token     = $tokenArr[1] ?? $tokenArr[0];
        }

        return $token;
    }
}
