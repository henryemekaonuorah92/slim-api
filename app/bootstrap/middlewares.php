<?php

$app->add(function (\Slim\Http\Request $request, \Slim\Http\Response $response, $next) {

    $bypass = \App\AppContainer::config('jwt')['bypass'] ?? [];
    $currentPath = $request->getUri()->getPath();

    // skip jwt if route match
    foreach ($bypass as $route) {
        if ($route == $currentPath) {
            return $response = $next($request, $response);
        }
    }


    $token = \App\Util\JWT\Jwt::fetchToken($request);
    if (!$token) {
        throw new Exception("Token not found", 401);
    }

    $tokenData = \App\Util\JWT\Jwt::decodeJwtToken($token);

    \App\AppContainer::setConfig('jwtToken', $token);
    \App\AppContainer::setConfig('jwtUser', $tokenData->data);

    $response = $next($request, $response);
    return $response;
});

//$app->add(new \App\Util\JWT\Jwt([
//    "path" => "/api",
//    'passthrough' => [
//        '/api/user/register',
//        '/api/user/login',
//        '/api/ping'
//    ],
//    "header" => "x-token",
//    "attribute" => "jwtUser",
//    'secret' => 'qwertyuiopasdfghjklzxcvbnm123456',
//    'error' => function (\Slim\Http\Request $request, \Slim\Http\Response $response, $arguments) {
//        throw new Exception($arguments["message"], 401);
//    }
//
//]));
