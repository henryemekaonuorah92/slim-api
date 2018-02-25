<?php

namespace App\Tag;

use App\Base\Controller\RestController;
use App\Tag\Model\TagModel;
use App\User\UserController;
use MongoDB\BSON\ObjectId;
use Slim\Http\Request;
use Slim\Http\Response;

class TagController extends RestController
{
    protected $modelClass = TagModel::class;

    /** @var \App\Tag\Model\TagModel */
    protected $model;

    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $args
     *
     * @throws \Exception
     */
    public function createTag(Request $request, Response $response, $args)
    {
        $objId = new ObjectId();
        $this->model->setData([
            'name'        => $request->getParsedBodyParam('name') ?? '',
            'description' => $request->getParsedBodyParam('description') ?? '',
            'color'       => $request->getParsedBodyParam('color') ?? '',
            'user_id'     => UserController::getUser()['_id'],
        ])->setId($objId)->save();

        $tag = $this->model->load($objId)->getStoredData();

        return $response->withJson($tag);
    }

    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $args
     */
    public function getAllTags(Request $request, Response $response, $args)
    {
        $query = $request->getParam('q');
        $tags = $this->model->getAllTags($query);

        return $response->withJson($tags, 200);
    }

    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $args
     */
    public function getUserAllTags(Request $request, Response $response, $args)
    {
        $tags = $this->model->getUserTags($args['user_id']);

        return $response->withJson($tags, 200);
    }

    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $args
     */
    public function getTagPosts(Request $request, Response $response, $args)
    {
        $params = $request->getParams();
        $posts = $this->model->getTagPosts($args['tag_id'], $params);

        return $response->withJson($posts, 200);
    }
}
