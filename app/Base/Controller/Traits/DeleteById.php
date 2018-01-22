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
trait DeleteById
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function delete(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $id = $args['id'] ?? null;

        $rs = $this->model->deleteOne(['_id' => new ObjectId($id)]);


        return $response->withJson(['n' => $rs->getDeletedCount()]);

    }
}