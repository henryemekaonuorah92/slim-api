<?php

declare(strict_types=1);

namespace Module\Group;

use Module\Core\Models\Base\MongoModel;

/**
 * Class UserModel
 * @property string $email
 * @property string $password
 * @property string $createdAt
 * @property string $updateAt
 * @package Module\Core\Models
 */
class GroupModel extends MongoModel
{
    /** @var string */
    protected $collectionNAme = 'groups';
}
