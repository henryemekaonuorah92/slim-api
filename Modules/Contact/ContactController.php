<?php

declare(strict_types=1);

namespace Modules\Contact;

use Core\Controllers\Base\RestController;

class ContactController extends RestController
{
    protected $modelClass = ContactModel::class;

}
