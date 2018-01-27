<?php

namespace App\Base\Controller\Traits;

use App\Base\Model\MongoDB;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Collection|MongoDB $model
 * @package App\Base\Controllers\Traits
 */
trait Save
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

        $rs = $this->model->setData($data)->save();

        return $response->withJson(['ok' => 1]);
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

        $data[$this->model->getIdFieldName()] = $objId = new ObjectId();
        $this->model->setData($data)->save();

        $rs = $this->model->load($objId)->getStoredData();

        return $response->withJson($rs);
    }
}