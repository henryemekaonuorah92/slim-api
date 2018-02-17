<?php

namespace Tests\Post;

use App\Post\PostModel;
use Tests\Base\BaseApiCase;

class RestTest extends BaseApiCase
{
    /**
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testPostFlow()
    {
        $idFieldName = (new PostModel())->getIdFieldName();
        // insert post
        $response = $this->sendHttpRequest(
            'POST', '/api/post',
            ['name' => 'post name', 'description' => 'post desc', 'testKey' => 'asdas']
        );

        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains('post name', $rs['name']);
        $this->assertContains('post desc', $rs['description']);
        $this->assertContains('asdas', $rs['testKey']);

        $postId = $rs[$idFieldName];


        // get all post
        $response = $this->sendHttpRequest(
            'GET', '/api/posts'
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertEquals(true, count($rs) >= 1);
        $this->assertEquals(true, is_array($rs[0]));
        $this->assertEquals(true, is_string($rs[0]['name']));


        // get post
        $response = $this->sendHttpRequest(
            'GET', '/api/post/' . $postId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains($postId, $rs[$idFieldName]);
        $this->assertContains('post desc', $rs['description']);

        // update post
        $response = $this->sendHttpRequest(
            'PUT', '/api/post/' . $postId,
            ['name' => 'post name update', 'description' => 'post desc update']

        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains('post desc', $rs['description']);

        // get post
        $response = $this->sendHttpRequest(
            'GET', '/api/post/' . $postId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains($postId, $rs[$idFieldName]);
        $this->assertContains('post desc update', $rs['description']);

        // delete post invalid id
        $response = $this->sendHttpRequest(
            'DELETE', '/api/post/' . '12312312'
        );
        $this->assertSame($response->getStatusCode(), 400);
        $rs = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $rs['message']);

        // delete post
        $response = $this->sendHttpRequest(
            'DELETE', '/api/post/' . $postId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertEquals(1, $rs['ok']);

        // get post
        $response = $this->sendHttpRequest(
            'GET', '/api/post/' . $postId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertEquals(null, $rs);

        // get post exception
        $response = $this->sendHttpRequest(
            'GET', '/api/post/' . '12312'
        );
        $this->assertSame($response->getStatusCode(), 400);
        $rs = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $rs['message']);

        // update post exception
        $response = $this->sendHttpRequest(
            'PUT', '/api/post/' . '12312',
            ['name' => 'post name update', 'description' => 'post desc update']
        );
        $this->assertSame($response->getStatusCode(), 400);
        $rs = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $rs['message']);

    }
}