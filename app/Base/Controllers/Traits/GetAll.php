<?php

namespace App\Base\Controllers\Base\Traits;

use App\Base\Models\Base\MongoModel;
use MongoDB\Collection;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Collection|MongoModel $model
 * @package App\Base\Controllers\Base\Traits
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
        $rs = $this->model->find()->toArray();
        return $response->withJson($rs);
    }
}