<?php

namespace Tests\Post;

use App\Post\Model\PostModel;
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
            ['title' => 'post name', 'content' => 'post desc']
        );

        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains('post name', $rs['title']);
        $this->assertContains('post desc', $rs['content']);

        $postId = $rs[$idFieldName];


        // get all post
        $response = $this->sendHttpRequest(
            'GET', '/api/posts'
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertEquals(true, count($rs) >= 1);
        $this->assertEquals(true, is_array($rs['data'][0]));
        $this->assertEquals(true, is_string($rs['data'][0]['title']));


        // get post
        $response = $this->sendHttpRequest(
            'GET', '/api/post/' . $postId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains($postId, $rs[0][$idFieldName]);
        $this->assertContains('post desc', $rs['0']['content']);

        // update post
        $response = $this->sendHttpRequest(
            'PUT', '/api/post/' . $postId,
            ['title' => 'post name update', 'content' => 'post desc update']

        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains('post desc', $rs['content']);

        // get post
        $response = $this->sendHttpRequest(
            'GET', '/api/post/' . $postId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains($postId, $rs[0][$idFieldName]);
        $this->assertContains('post desc update', $rs[0]['content']);

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
        $this->assertTrue(empty($rs));

        // get post exception
        $response = $this->sendHttpRequest(
            'GET', '/api/post/' . '12312'
        );
        $this->assertSame($response->getStatusCode(), 500);
        $rs = $this->responseDataArr();
        $this->assertEquals('Not a valid object id.', $rs['message']);

        // update post exception
        $response = $this->sendHttpRequest(
            'PUT', '/api/post/' . '12312',
            ['title' => 'post name update', 'content' => 'post desc update']
        );
        $this->assertSame($response->getStatusCode(), 400);
        $rs = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $rs['message']);

    }
}