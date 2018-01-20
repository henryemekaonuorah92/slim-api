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

        return $response->withJson($rs);
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

        $this->model->insertDoc($data);

        // todo check why object if is alaways different
        //$rs = $this->model->findOne(['_id' => $data['_id']]);

        return $response->withJson($data);
    }
}