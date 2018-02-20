<?php

namespace App\Tag;

use App\Base\Model\MongoDB;

/**
 * Class UserModel
 * @property string $email
 * @property string $password
 * @property string $createdAt
 * @property string $updateAt
 * @package App\Base\Models
 */
class TagModel extends MongoDB
{
    /** @var string */
    protected $collectionNAme = 'tag';
}
