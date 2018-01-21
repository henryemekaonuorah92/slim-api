<?php


$app->add(function (\Slim\Http\Request $request, \Slim\Http\Response $response, $next) {

    $bypass = \Core\AppContainer::config('jwt')['bypass'] ?? [];
    $currentPath = $request->getUri()->getPath();
    // skip jwt if route match
    foreach ($bypass as $route) {
        if ($route == $currentPath) {
            return $response = $next($request, $response);
        }
    }


    $token = \Util\JWT\Jwt::fetchToken($request);
    if (!$token) {
        throw new Exception("Token not found", 401);
    }

    $tokenData = \Util\JWT\Jwt::decodeJwtToken($token);

    \Core\AppContainer::setConfig('jwtToken', $token);
    \Core\AppContainer::setConfig('jwtUser', $tokenData->data);

    $response = $next($request, $response);
    return $response;
});