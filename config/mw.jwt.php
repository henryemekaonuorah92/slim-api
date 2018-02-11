<?php


$app->add(function (\Slim\Http\Request $request, \Slim\Http\Response $response, $next) {

    $enabled = \App\Base\AppContainer::config('jwt')['enabled'] ?? true;
    if (!$enabled) {
        return $response = $next($request, $response);
    }

    $bypass = \App\Base\AppContainer::config('jwt')['bypass'] ?? [];

    $currentPath = $request->getUri()->getPath();
    $currentMethod = strtolower($request->getMethod());

    if ($currentMethod == 'options') {
        return $response;
    }

    // skip jwt if route match
    foreach ($bypass as $route) {

        list($method, $path) = explode(' ', $route);

        $method = strtolower($method);

        if ($path == $currentPath && $method == $currentMethod) {
            return $response = $next($request, $response);
        }
    }


    $token = \App\Base\Helper\Jwt::fetchToken($request);
    if (!$token) {
        throw new Exception("Token not found", 401);
    }

    $tokenData = \App\Base\Helper\Jwt::decodeJwtToken($token);

    \App\Base\AppContainer::setConfig('jwtToken', $token);
    \App\Base\AppContainer::setConfig('jwtUser', (array)$tokenData->data);

    $response = $next($request, $response);

    return $response;
});
