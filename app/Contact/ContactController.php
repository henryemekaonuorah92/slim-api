<?php

declare(strict_types=1);

namespace App\Contact;

use App\Core\Controllers\Base\RestController;

class ContactController extends RestController
{
    protected $modelClass = ContactModel::class;

}
