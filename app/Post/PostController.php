<?php

namespace App\Post;

use App\Base\Controller\RestController;
use App\Post\Model\PostModel;
use App\Post\Model\PostTagModel;
use MongoDB\BSON\ObjectId;
use Slim\Http\Request;
use Slim\Http\Response;

class PostController extends RestController
{
    protected $modelClass = PostModel::class;

    /** @var \App\Post\Model\PostModel */
    protected $model;

    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $args
     *
     * @throws \Exception
     */
    public function createPost(Request $request, Response $response, $args)
    {
        $objId = new ObjectId();
        $this->model->setData([
            'title'   => $request->getParsedBodyParam('title'),
            'content' => $request->getParsedBodyParam('content'),
            'user_id' => $request->getParsedBodyParam('user_id')
        ])->setId($objId)->save();

        $post = $this->model->load($objId)->getStoredData();

        $tags = explode(',', $request->getParsedBodyParam('tags'));
        foreach ($tags as $tagId) {
            (new PostTagModel())->setData([
                'post_id' => $post->_id,
                'tag_id'  => trim($tagId),
            ])->save();
        }

        return $response->withJson($post);
    }

    public function getUserAllPosts(Request $request, Response $response, $args)
    {
        $userId = $args['user_id'];
        $posts  = $this->model->getUserPosts($userId);

        return $response->withJson($posts, 200);
    }
}