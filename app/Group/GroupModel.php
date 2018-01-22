<?php

declare(strict_types=1);

namespace App\Group;

use App\Base\Models\MongoModel;

/**
 * Class UserModel
 * @property string $email
 * @property string $password
 * @property string $createdAt
 * @property string $updateAt
 * @package App\Base\Models
 */
class GroupModel extends MongoModel
{
    /** @var string */
    protected $collectionNAme = 'groups';
}
