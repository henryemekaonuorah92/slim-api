<?php

namespace App\Post\Model;

use App\Base\Model\MongoDB;

/**
 * @property string $_id
 * @property string $post_id
 * @property string $tag_id
 * @property string $createdAt
 * @property string $updateAt
 */
class PostTagModel extends MongoDB
{
    /** @var string $collectionName */
    protected $collectionName = 'post_tag';

    /**
     * Post can contains many tags.
     *
     * @param string $postId
     * @param string $tagId
     *
     * @throws \Exception
     */
    public function createPostTags(string $postId, string $tagId): void
    {
        $this->setData([
            'post_id' => $postId ?? '',
            'tag_id'  => trim($tagId) ?? '',
        ])->save();
    }
}