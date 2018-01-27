<?php

namespace App\Contact;

use App\Base\Model\MongoDB;


/**
 * Class UserModel
 * @property string $email
 * @property string $password
 * @property string $createdAt
 * @property string $updateAt
 * @package App\Base\Models
 */
class ContactModel extends MongoDB
{
    /** @var string */
    protected $collectionNAme = 'contacts';
}
