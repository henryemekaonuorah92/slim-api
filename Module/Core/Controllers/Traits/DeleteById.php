<?php

namespace Module\Core\Controllers\Base\Traits;

use Module\Core\Models\Base\MongoModel;
use Meabed\Mongoose\Method;
use MongoDB\BSON\ObjectId;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Method|MongoModel $model
 * @package Module\Core\Controllers\Base\Traits
 */
trait DeleteById
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return array|\MongoDB\Driver\WriteResult
     * @throws \Exception
     */
    public function delete(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $id = $args['id'] ?? null;

        $rs = $this->model->deleteOne(['_id' => new ObjectId($id)]);

        return $rs;

    }
}