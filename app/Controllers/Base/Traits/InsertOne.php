<?php

namespace App\Controllers\Base\Traits;

use Meabed\Mongoose\Method;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Method $model
 * @package App\Controllers\Base\Traits
 */
trait InsertOne
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return array|\MongoDB\Driver\WriteResult
     * @throws \Exception
     */
    public function insert(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $rs = $this->model->insert($this->request->getParsedBody());
        return $rs;
    }
}