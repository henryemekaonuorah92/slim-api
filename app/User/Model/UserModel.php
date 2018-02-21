<?php

namespace App\User\Model;

use App\Base\Helper\Password;
use App\Base\Model\MongoDB;

/**
 * @property string $email
 * @property string $password
 * @property string $createdAt
 * @property string $updateAt
 */
class UserModel extends MongoDB
{
    /** @var string */
    protected $collectionName = 'user';

    /** @var array */
    protected $_rules = [
        'email' => ['required', 'email'],
        'password' => ['required', ['lengthMin', 6]],
    ];

    /**
     * @return $this
     * @throws \Exception
     */
    public function _beforeSave()
    {
        $this->password = Password::hash($this->password);
        parent::_beforeSave();
        return $this;
    }
}
