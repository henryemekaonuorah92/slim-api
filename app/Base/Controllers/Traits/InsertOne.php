<?php

namespace App\Base\Controllers\Base\Traits;

use App\Base\Models\Base\MongoModel;
use Meabed\Mongoose\Method;
use MongoDB\BSON\ObjectId;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Method|MongoModel $model
 * @package App\Base\Controllers\Base\Traits
 */
trait InsertOne
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function insert(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $data = $this->request->getParsedBody();

        $rs = $this->model->insertDoc($data);

        return $response->withJson(['n' => $rs->getInsertedCount()]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function insertAndRetrieve(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;
        $data = $this->request->getParsedBody();

        $data['_id'] = $objId = new ObjectId();
        $this->model->insertDoc($data);

        $rs = $this->model->findOne(['_id' => $objId]);

        return $response->withJson($rs);
    }
}