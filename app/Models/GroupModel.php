<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Base\MongoModel;

/**
 * Class UserModel
 * @property string $email
 * @property string $password
 * @property string $createdAt
 * @property string $updateAt
 * @package App\Models
 */
class GroupModel extends MongoModel
{
    /** @var string */
    protected $collectionNAme = 'groups';
}
