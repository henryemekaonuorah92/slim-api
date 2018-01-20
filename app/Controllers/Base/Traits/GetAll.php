<?php

namespace App\Controllers\Base\Traits;

use App\Models\Base\MongoModel;
use Meabed\Mongoose\Method;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Method|MongoModel $model
 * @package App\Controllers\Base\Traits
 */
trait GetAll
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function getAll(Request $request, Response $response, $args)
    {
        $rs = $this->model->find();
        return $response->withJson($rs);
    }
}