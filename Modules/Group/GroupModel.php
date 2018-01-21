<?php

declare(strict_types=1);

namespace Modules\Group;

use Core\Models\Base\MongoModel;

/**
 * Class UserModel
 * @property string $email
 * @property string $password
 * @property string $createdAt
 * @property string $updateAt
 * @package Core\Models
 */
class GroupModel extends MongoModel
{
    /** @var string */
    protected $collectionNAme = 'groups';
}
