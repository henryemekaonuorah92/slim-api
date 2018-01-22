<?php


$app->add(function (\Slim\Http\Request $request, \Slim\Http\Response $response, $next) {

    $bypass = \App\Base\AppContainer::config('jwt')['bypass'] ?? [];
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

    \App\Base\AppContainer::setConfig('jwtToken', $token);
    \App\Base\AppContainer::setConfig('jwtUser', $tokenData->data);

    $response = $next($request, $response);
    return $response;
});