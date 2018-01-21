<?php

namespace Core\Controllers\Base\Traits;

use Core\Models\Base\MongoModel;
use Meabed\Mongoose\Method;
use MongoDB\BSON\ObjectId;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Method|MongoModel $model
 * @package Core\Controllers\Base\Traits
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
        try {
            $mongoId = new ObjectId($id);
        } catch (\Exception $ex) {
            throw new \Exception('Invalid ID', 400);
        }
        $rs = $this->model->findOne(['_id' => $mongoId]);
        return $this->response->withJson($rs);
    }

}