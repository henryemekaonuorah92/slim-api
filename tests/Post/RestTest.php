<?php

namespace Tests\Post;

use App\Post\Model\PostModel;
use Tests\Base\BaseApiCase;

class RestTest extends BaseApiCase
{
    /**
     * @var \App\Post\Model\PostModel $model
     */
    private $model;

    /**
     * @var array
     */
    private $examplePost = [
        'title'   => 'post name',
        'content' => 'post desc',

    ];

    private $idFieldName;

    public function setUp()
    {
        $this->model       = new PostModel();
        $this->idFieldName = $this->model->getIdFieldName();
    }

    /**
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testPostFlow()
    {
        // Insert post
        $response = $this->sendHttpRequest('POST', '/api/post', $this->examplePost);
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr();
        $this->assertEquals($this->examplePost['title'], $res['title']);
        $this->assertEquals($this->examplePost['content'], $res['content']);

        $postId = $res[$this->idFieldName];

        // Get all post
        $response = $this->sendHttpRequest('GET', '/api/posts');
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr()['data'][0];
        $this->assertTrue(is_array($res));
        $this->assertEquals($this->examplePost['title'], $res['title']);
        $this->assertEquals($this->examplePost['content'], $res['content']);


        // Get post
        $response = $this->sendHttpRequest('GET', '/api/post/' . $postId);
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr()[0];
        $this->assertContains($postId, $res[$this->idFieldName]);
        $this->assertContains('post desc', $res['content']);

        // Update post
        $updatePostData = [
            'title'   => 'post title update',
            'content' => 'post content update',
        ];
        $response = $this->sendHttpRequest('PUT', "/api/post/{$postId}", $updatePostData);
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr();
        $this->assertEquals('post title update', $res['title']);
        $this->assertEquals('post content update', $res['content']);

        // Get post
        $response = $this->sendHttpRequest('GET', "/api/post/{$postId}");
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr()[0];
        $this->assertContains($postId, $res[$this->idFieldName]);
        $this->assertContains($updatePostData['content'], $res['content']);

        // Delete post invalid id
        $response = $this->sendHttpRequest('DELETE', '/api/post/12312312');
        $this->assertSame($response->getStatusCode(), 400);
        $res = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $res['message']);

        // Delete post
        $response = $this->sendHttpRequest('DELETE', "/api/post/{$postId}");
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr();
        $this->assertTrue($res['deleted']);

        // Get post
        $response = $this->sendHttpRequest('GET', "/api/post/{$postId}");
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr();
        $this->assertTrue(empty($res));

        // Get post exception
        $response = $this->sendHttpRequest('GET', '/api/post/12312');
        $this->assertSame($response->getStatusCode(), 500);
        $res = $this->responseDataArr();
        $this->assertEquals('Not a valid object id.', $res['message']);

        // Update post exception
        $response = $this->sendHttpRequest('PUT', '/api/post/12312', $this->examplePost);
        $this->assertSame($response->getStatusCode(), 400);
        $res = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $res['message']);
    }
}
