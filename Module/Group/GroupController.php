<?php

declare(strict_types=1);

namespace Module\Group;

use Module\Core\Controllers\Base\RestController;

class GroupController extends RestController
{
    protected $modelClass = GroupModel::class;

}
