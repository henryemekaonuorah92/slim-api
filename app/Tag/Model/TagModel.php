<?php

namespace App\Tag\Model;

use App\Base\AppContainer;
use App\Base\Model\MongoDB;

/**
 * Class TagModel
 *
 * @property string $name
 * @property string $description
 * @property string $color
 * @property string $user_id
 * @property string $createdAt
 * @property string $updateAt
 */
class TagModel extends MongoDB
{
    /** @var string */
    protected $collectionName = 'tag';

    /** @var array */
    protected $_rules = [
        'name'  => ['required'],
        'color' => ['required'],
    ];
}
