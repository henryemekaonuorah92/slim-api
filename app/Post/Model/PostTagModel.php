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
     * @param string $tagId
     *
     * @throws \Exception
     */
    public function createPostTags($postId, string $tagId): void
    {
        $this->setData([
            'post_id' => $postId ?? '',
            'tag_id' => $tagId ?? '',
        ])->save();
    }

    /**
     * @param $postId
     *
     * @return bool
     * @throws \Exception
     */
    public function deletePostTags($postId): bool
    {
        $this->getResourceCollection()->deleteMany([
            'post_id' => $postId,
        ]);

        return true;
    }

    /**
     * @param $postId
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteTagPosts($tagId): bool
    {
        $this->getResourceCollection()->deleteMany([
            'tag_id' => $tagId,
        ]);

        return true;
    }
}
