<?php

declare(strict_types=1);

namespace Module\Contact;

use Module\Core\Controllers\Base\RestController;

class ContactController extends RestController
{
    protected $modelClass = ContactModel::class;

}
