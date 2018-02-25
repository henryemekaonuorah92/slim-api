<?php

namespace App\Tag\Model;

use App\Base\Helper\PaginationHelper;
use App\Base\Model\MongoDB;
use App\Post\Model\PostModel;
use App\Post\Model\PostTagModel;
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

    public function getAllTags(string $query = null)
    {
        $searchTerm = $query ?? '';

        $finalFilters = [];

        if (!empty($searchTerm)) {
            $finalFilters['$and'][] = [
                'name' => [
                    '$regex'   => $searchTerm,
                    '$options' => 'i',
                ],
            ];
        }

        $tags = $this->getResourceCollection()->find($finalFilters)->toArray();

        // populate user details inside each post
        $tags = UserModel::populateUserDetail($tags);

        return $tags;
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

    /**
     * @param string $tagId
     */
    public function getTagPosts($tagId, $filters)
    {
        $limit = (int)($filters['limit'] ?? 10);
        $page  = (int)($filters['page'] ?? 1);
        $skip  = ($page - 1) * $limit;
        $sort  = ['created_at' => -1];

        $finalFilters = [];

        $postIds = $this->getTagPostIds($tagId);

        $postModel = new PostModel();
        $total     = $postModel->getResourceCollection()->count($finalFilters);
        $posts     = $postModel->getResourceCollection()->find([
            '_id' => [
                '$in' => $postIds,
            ],
        ], [
            'limit' => $limit,
            'skip'  => $skip,
            'sort'  => $sort,
        ])->toArray();

        $posts = $this->populateUserDetail($posts);

        $pagination = new PaginationHelper();

        return $pagination->paginate($posts, $total, $limit, $page);
    }

    /**
     * @param string $tagId
     *
     * @return array
     */
    public function getTagPostIds(string $tagId): array
    {
        $postTagModel = new PostTagModel();
        $postTag      = $postTagModel->getResourceCollection()->find([
            'tag_id' => $tagId,
        ])->toArray();

        $postIds = array_column($postTag, 'post_id');

        foreach ($postIds as $key => $postId) {
            $postObjId     = new ObjectId($postId);
            $postIds[$key] = $postObjId;
        }

        return $postIds;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public function populateUserDetail(array $posts): array
    {
        $userIds = array_column($posts, 'user_id');

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
                '_id'       => 1,
                'email'     => 1,
                'firstname' => 1,
                'lastname'  => 1,
            ],
        ])->toArray();

        // transform users key by user_id
        $usersKeyBy = [];
        foreach ($users as $user) {
            $usersKeyBy[$user['_id']] = $user;
        }

        foreach ($posts as &$post) {
            $post['user_detail'] = $usersKeyBy[$post['user_id']] ?? [];
        }

        return $posts;
    }

    /**
     * @param string $tagId
     *
     * @return $this
     * @throws \Exception
     */
    public function deleteTag(string $tagId)
    {
        return $this->delete($tagId);
    }
}
