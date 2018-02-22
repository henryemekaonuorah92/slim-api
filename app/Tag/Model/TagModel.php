<?php

namespace App\Tag\Model;

use App\Base\Helper\PaginationHelper;
use App\Base\Model\MongoDB;
use App\User\Model\UserModel;
use MongoDB\BSON\ObjectId;

/**
 * Class TagModel
 *
 * @property string $name
 * @property string $description
 * @property string $color
 * @property string $user_id
 * @property string $createdAt
 * @property string $updateAt
 */
class TagModel extends MongoDB
{
    /** @var string */
    protected $collectionName = 'tag';

    /** @var array */
    protected $_rules = [
        'name'  => ['required'],
        'color' => ['required'],
    ];

    public function getAllTags()
    {
        $limit = (int)($filters['limit'] ?? 10);
        $page  = (int)($filters['page'] ?? 1);
        $skip  = ($page - 1) * $limit;
        $sort  = ['created_at' => -1];

        $searchTerm = $filters['query'] ?? '';

        $finalFilters = [];

        if (!empty($searchTerm)) {
            $finalFilters['$and'][] = [
                'name' => [
                    '$regex'   => $searchTerm,
                    '$options' => 'i',
                ],
            ];
        }

        $total = $this->getResourceCollection()->count($finalFilters);
        $tags  = $this->getResourceCollection()->find($finalFilters, [
            'limit' => $limit,
            'skip'  => $skip,
            'sort'  => $sort,
        ])->toArray();

        // populate user details inside each post
        $tags = UserModel::populateUserDetail($tags);

        $pagination = new PaginationHelper();

        return $pagination->paginate($tags, $total, $limit, $page);
    }

    /**
     * @param array $tagIds
     *
     * @return array
     */
    public function getTagByIds(array $tagIds): array
    {
        $tagObjIds = array_map(function ($tagId) {
            return new ObjectId($tagId);
        }, $tagIds);

        $tagDetails = $this->getResourceCollection()->find([
            '_id' => [
                '$in' => $tagObjIds,
            ],
        ])->toArray();

        return $tagDetails;
    }

    /**
     * Get all tags that are created by a user.
     *
     * @param string $userId
     *
     * @return array
     */
    public function getUserTags(string $userId): array
    {
        $tag = $this->getResourceCollection()->find([
            'user_id' => $userId,
        ])->toArray();

        return $tag;
    }
}
