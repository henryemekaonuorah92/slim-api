<?php

namespace App\Post\Model;

use App\Base\Model\MongoDB;

/**
 * Class PostModel
 *
 * @property string $title
 * @property string $content
 * @property string $createdAt
 * @property string $updateAt
 */
class PostModel extends MongoDB
{
    /** @var string */
    protected $collectionName = 'post';

    /** @var array */
    protected $_rules = [
        'title'   => ['required'],
        'content' => ['required'],
    ];

    public function getUserPosts(string $userId)
    {
        $posts = $this->getResourceCollection()->find([
            'user_id' => $userId,
        ])->toArray();

        return $posts;
    }
}
