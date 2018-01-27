<?php


namespace App\User;

use App\Base\Helper\Password;
use App\Base\Model\MongoDB;

/**
 * Class UserModel
 * @property string $email
 * @property string $password
 * @property string $createdAt
 * @property string $updateAt
 * @package App\Base\Models
 */
class UserModel extends MongoDB
{
    /** @var string */
    protected $collectionNAme = 'users';

    /** @var array */
    protected $rules = [
        'email' => ['required', 'email'],
        'password' => ['required', ['lengthMin', 6]],
    ];


    /**
     * @return $this
     */
    public function _beforeSave()
    {
        $this->password = Password::hash($this->password);
        parent::_beforeSave();
        return $this;
    }
}
