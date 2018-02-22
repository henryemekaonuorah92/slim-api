<?php

namespace App\Post\Model;

use __;
use App\Base\Helper\PaginationHelper;
use App\Base\Model\MongoDB;
use App\Tag\Model\TagModel;
use App\User\Model\UserModel;
use App\User\UserController;
use MongoDB\BSON\ObjectId;
use Slim\Http\Request;

/**
 * Class PostModel
 *
 * @property string $title
 * @property string $content
 * @property string $createdAt
 * @property string $updateAt
 */
class PostModel extends MongoDB
{
    /** @var string */
    protected $collectionName = 'post';

    /** @var array */
    protected $_rules = [
        'title'   => ['required'],
        'content' => ['required'],
    ];

    public function getUserPosts(string $userId)
    {
        $posts = $this->getResourceCollection()->find([
            'user_id' => $userId,
        ])->toArray();

        return $posts;
    }

    /**
     * @param \Slim\Http\Request $request
     *
     * @throws \Exception
     */
    public function createPost(Request $request)
    {
        $objId = new ObjectId();

        $this->setData([
            'title'   => $request->getParsedBodyParam('title'),
            'content' => $request->getParsedBodyParam('content'),
            'user_id' => UserController::getUser()['_id'],
        ])->setId($objId)->save();

        $post = $this->load($objId)->getStoredData();

        return $post;
    }

    /**
     * @param array $filters
     *
     * @return array
     */
    public function getAllPosts(array $filters): array
    {
        $limit = (int)($filters['limit'] ?? 10);
        $page  = (int)($filters['page'] ?? 1);
        $skip  = ($page - 1) * $limit;
        $sort  = ['created_at' => -1];

        $searchTerm = $filters['query'] ?? '';

        $finalFilters = [];

        if (!empty($searchTerm)) {
            $finalFilters['$and'][] = [
                'title' => [
                    '$regex'   => $searchTerm,
                    '$options' => 'i',
                ],
            ];
        }

        $total = $this->getResourceCollection()->count($finalFilters);
        $posts = $this->getResourceCollection()->find($finalFilters, [
            'limit' => $limit,
            'skip'  => $skip,
            'sort'  => $sort,
        ])->toArray();

        // populate user details inside each post
        $posts = $this->populateUserDetail($posts);
        $posts = $this->populateTagDetail($posts);

        $pagination = new PaginationHelper();

        return $pagination->paginate($posts, $total, $limit, $page);
    }

    /**
     * @param array $posts
     *
     * @return array
     */
    public function populateTagDetail(array $posts): array
    {
        $postIds = array_column($posts, '_id');

        $postTagModel = new PostTagModel();
        $postTags     = $postTagModel->getResourceCollection()->find([
            'post_id' => [
                '$in' => $postIds,
            ],
        ], [
            'projection' => [
                'post_id' => 1,
                'tag_id'  => 1,
            ],
        ])->toArray();

        $tagIds     = array_column($postTags, 'tag_id');
        $tagsDetail = (new TagModel)->getTagByIds($tagIds);

        $tagsKeyByTagId = [];
        foreach ($tagsDetail as $tagDetail) {
            $tagsKeyByTagId[$tagDetail['_id']] = $tagDetail;
        }

        $tagsGroupByPostId = __::groupBy($postTags, 'post_id');


        foreach ($posts as &$post) {
            $postTagIds = $tagsGroupByPostId[$post['_id']] ?? [];

            $tags = [];
            foreach ($postTagIds as $postTagId) {

                $detail = $tagsKeyByTagId[$postTagId['tag_id']];
                if (!empty($detail)) {
                    $tags[] = (array)$detail;
                }
            }

            $post['tags'] = $tags;
        }

        return $posts;
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

        foreach ($posts as &$post) {
            $post['user_detail'] = $usersKeyBy[$post['user_id']] ?? [];
        }

        return $posts;
    }

    /**
     * Get a post by its id.
     *
     * @param string $postId
     */
    public function getPost(string $postId)
    {
        try {
            $postObjIds = new ObjectId($postId);
        } catch (\Exception $e) {
            throw new \RuntimeException('Not a valid object id.');
        }

        $postDetails = $this->getResourceCollection()->find([
            '_id' => $postObjIds
        ])->toArray();

        $postDetails = $this->populateUserDetail($postDetails);
        $postDetails = $this->populateTagDetail($postDetails);

        return (array)$postDetails;
    }
}