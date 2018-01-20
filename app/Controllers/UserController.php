<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Base\RestController;
use App\Models\UserModel;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends RestController
{
    protected $modelClass = UserModel::class;

    public function me(Request $request, Response $response, $args)
    {
        $rs = $request->getAttribute('jwtUser');
        return $response->withJson($rs);
    }

    public function login(Request $request, Response $response, $args)
    {
        $rs = ['xx'];
        return $response->withJson($rs);
    }
}
