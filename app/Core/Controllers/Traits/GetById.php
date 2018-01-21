<?php

namespace App\Core\Controllers\Base\Traits;

use App\Core\Models\Base\MongoModel;
use Meabed\Mongoose\Method;
use MongoDB\BSON\ObjectId;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Method|MongoModel $model
 * @package App\Core\Controllers\Base\Traits
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