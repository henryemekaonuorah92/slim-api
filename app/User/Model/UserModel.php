<?php

namespace App\User\Model;

use App\Base\Helper\Password;
use App\Base\Model\MongoDB;
use MongoDB\BSON\ObjectId;

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
        'email'    => ['required', 'email'],
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

    /**
     * @param array $array
     *
     * @return array
     */
    public static function populateUserDetail(array $array): array
    {
        $userIds = array_column($array, 'user_id');

        $userObjIds = array_map(function ($userId) {
            return new ObjectId($userId);
        }, $userIds);

        $userModel = new UserModel();
        $users     = $userModel->getResourceCollection()->find([
            '_id' => [
                '$in' => $userObjIds,
            ],
        ], [
            'projection' => [
                '_id'        => 1,
                'email'      => 1,
                'first_name' => 1,
                'last_name'  => 1,
            ],
        ])->toArray();

        // transform users key by user_id
        $usersKeyBy = [];
        foreach ($users as $user) {
            $usersKeyBy[$user['_id']] = $user;
        }

        foreach ($array as &$post) {
            $post['user_detail'] = $usersKeyBy[$post['user_id']] ?? [];
        }

        return $array;
    }
}
