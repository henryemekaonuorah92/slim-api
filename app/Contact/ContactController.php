<?php

declare(strict_types=1);

namespace App\Contact;

use App\Base\Controller\RestController;

class ContactController extends RestController
{
    protected $modelClass = ContactModel::class;

}
