<?php

declare(strict_types=1);

namespace Modules\Group;

use Core\Controllers\Base\RestController;

class GroupController extends RestController
{
    protected $modelClass = GroupModel::class;

}
