<?php

namespace App\User\Model;

use App\Base\Model\MongoDB;

/**
 * @property string $user_id
 * @property string $post_id
 * @property string $createAt
 * @property string $updateAt
 */
class UserPostModel extends MongoDB
{
    /** @var string */
    protected $collectionName = 'user_post';
}