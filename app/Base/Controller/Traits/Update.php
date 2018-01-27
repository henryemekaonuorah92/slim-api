<?php

namespace App\Base\Controller\Traits;

use App\Base\Model\MongoDB;
use MongoDB\Collection;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Collection|MongoDB $model
 * @package App\Base\Controllers\Traits
 */
trait Update
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws \Exception
     */
    public function updateAndRetrieve(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $id = $args['id'] ?? null;

        $updateData = $request->getParsedBody() ?? [];

        // todo
        $this->model->setData($updateData)->update($id);

        $rs = $this->model->load($id)->getStoredData();

        return $response->withJson($rs ?: null);
    }
}