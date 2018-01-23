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
trait UpdateById
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function update(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $id = $args['id'] ?? null;
        $updateData = $request->getParsedBody() ?? [];

        $rs = $this->model->updateDocById($id, $updateData);

        return $response->withJson(['n' => $rs->getModifiedCount()]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function updateAndRetrieve(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $id = $args['id'] ?? null;
        $updateData = $request->getParsedBody() ?? [];

        $rs = $this->model->updateDocById($id, $updateData);

        $rs = $this->model->findOne(['_id' => new ObjectId($id)]);

        return $response->withJson($rs);
    }
}