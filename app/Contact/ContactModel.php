<?php

declare(strict_types=1);

namespace App\Contact;

use App\Core\Models\Base\MongoModel;

/**
 * Class UserModel
 * @property string $email
 * @property string $password
 * @property string $createdAt
 * @property string $updateAt
 * @package App\Core\Models
 */
class ContactModel extends MongoModel
{
    /** @var string */
    protected $collectionNAme = 'contacts';
}
