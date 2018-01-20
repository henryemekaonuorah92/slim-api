<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Base\RestController;
use App\Models\ContactModel;

class ContactController extends RestController
{
    protected $modelClass = ContactModel::class;

}
