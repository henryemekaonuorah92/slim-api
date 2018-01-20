<?php

namespace App\Controllers\Base\Traits;

use Meabed\Mongoose\Method;
use MongoDB\BSON\ObjectId;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Method $model
 * @package App\Controllers\Base\Traits
 */
trait GetById
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return static
     * @throws \Exception
     */
    public function get(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $id = $args['id'] ?? null;

        $rs = $this->model->findOne(['_id' => new ObjectId($id)]);
        return $this->response->withJson($rs);
    }

}