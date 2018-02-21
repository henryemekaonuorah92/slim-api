<?php

namespace App\User\Model;

use App\Base\Model\MongoDB;

/**
 * @property string $user_id
 * @property string $tag_id
 * @property string $createAt
 * @property string $updateAt
 */
class UserTagModel extends MongoDB
{
    /** @var string */
    protected $collectionName = 'user_tag';
}