<?php

namespace App\Post;

use App\Base\Controller\RestController;
use App\Post\Model\PostModel;
use App\Post\Model\PostTagModel;
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
        $post = $this->model->createPost($request);

        if (!is_null($request->getParsedBodyParam('tags'))) {
            $tags = $request->getParsedBodyParam('tags');
            foreach ($tags as $tagId) {
                (new PostTagModel())->createPostTags($post->_id, $tagId['_id']);
            }
        }

        return $response->withJson($post);
    }

    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $args
     *
     * @throws \Exception
     */
    public function updatePost(Request $request, Response $response, $args)
    {
        $postId = $args['post_id'];
        $post   = $this->model->updatePost($postId, $request);

        if (!is_null($request->getParsedBodyParam('tags'))) {
            $tags = $request->getParsedBodyParam('tags');
            (new PostTagModel())->deletePostTags($post->_id);

            foreach ($tags as $tagId) {
                $postModel = new PostTagModel();
                $postModel->createPostTags($post->_id, $tagId['_id']);
            }
        }

        return $response->withJson($post);
    }

    public function getUserAllPosts(Request $request, Response $response, $args)
    {
        $userId = $args['user_id'];
        $posts  = $this->model->getUserPosts($userId);

        return $response->withJson($posts, 200);
    }

    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $args
     */
    public function getAllPosts(Request $request, Response $response, $args)
    {
        $params = $request->getParams();

        return $response->withJson($this->model->getAllPosts($params));
    }

    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $args
     */
    public function getPost(Request $request, Response $response, $args)
    {
        $post = $this->model->getPost($args['post_id']);

        return $response->withJson($post, 200);
    }

    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $args
     *
     * @throws \Exception
     */
    public function deletePost(Request $request, Response $response, $args)
    {
        $postId = $args['post_id'];

        $this->model->deletePost($postId);
        (new PostTagModel())->deletePostTags($postId);

        return $response->withJson([
            'deleted' => true,
        ], 200);
    }
}
