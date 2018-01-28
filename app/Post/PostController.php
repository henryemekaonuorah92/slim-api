<?php

namespace App\Post;

use App\Base\Controller\RestController;

class PostController extends RestController
{
    protected $modelClass = PostModel::class;
}
