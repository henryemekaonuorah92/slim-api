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
    public function updateAndRetrieve(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $id = $args['id'] ?? null;
        try {
            $mongoId = new ObjectId($id);
        } catch (\Exception $ex) {
            throw new \Exception('Invalid ID', 400);
        }

        $updateData = $request->getParsedBody() ?? [];

        // todo
        $this->model->setData($updateData)->update($id);

        $rs = $this->model->load(new ObjectId($mongoId))->getStoredData();

        return $response->withJson($rs);
    }
}