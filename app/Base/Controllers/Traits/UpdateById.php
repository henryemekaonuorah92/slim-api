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

        return $response->withJson($rs);

    }
}