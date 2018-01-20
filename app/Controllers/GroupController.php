<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Base\RestController;
use App\Models\GroupModel;

class GroupController extends RestController
{
    protected $modelClass = GroupModel::class;

}
