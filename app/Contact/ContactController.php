<?php

declare(strict_types=1);

namespace App\Contact;

use App\Base\Controllers\RestController;

class ContactController extends RestController
{
    protected $modelClass = ContactModel::class;

}
