<?php

declare(strict_types=1);

namespace App\Group;

use App\Core\Controllers\Base\RestController;

class GroupController extends RestController
{
    protected $modelClass = GroupModel::class;

}
