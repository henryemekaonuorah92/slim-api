<?php

namespace App\Controllers\Base\Traits;

use Meabed\Mongoose\Method;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Method $model
 * @package App\Controllers\Base\Traits
 */
trait CountBy
{

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return static
     * @throws \Exception
     */
    public function count(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $rs = $this->model->count();
        return $this->response->withJson($rs);
    }
}