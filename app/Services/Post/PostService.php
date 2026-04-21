<?php

namespace App\Services\Post;

use App\Repositories\Post\PostRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


/**
 * @property PostRepositoryInterface $repository
 */
class PostService extends BaseService
{
    public function __construct(PostRepositoryInterface $postRepository)
    {
        parent::__construct($postRepository);
    }

    /**
     * @throws Exception
     */
    public function createPost(array $data)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $data['user_id'] = $user->id;
            $data = $this->fillDefaultInformation($data, $user);

            $post = $this->repository->create($data);

            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Lỗi: " . $e->getMessage());
        }
    }

    /**
     *
     * @throws Exception
     */
    public function updatePost($id, array $data)
    {
        $post = $this->repository->findById($id);

        if (!$post || $post->user_id !== Auth::id()) {
            throw new Exception("Bạn không có quyền chỉnh sửa bài viết này.", 403);
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $data = $this->fillDefaultInformation($data, $user);

            $updatedPost = $this->repository->update($id, $data);

            DB::commit();

            return $updatedPost;
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception("Lỗi khi cập nhật tin đăng: " . $e->getMessage());
        }
    }

    public function getActivePosts(array $data)
    {
        return $this->repository->getActivePosts($data);
    }

    public function getMyPosts(array $data)
    {
        return $this->repository->getMyPosts($data);
    }

    /**
     * @param array $data
     * @param Authenticatable|null $user
     * @return array
     */
    public function fillDefaultInformation(array $data, ?Authenticatable $user): array
    {
        $contactFields = [
            'contact_name'  => 'name',
            'contact_phone' => 'phone',
            'contact_email' => 'email'
        ];
        foreach ($contactFields as $field => $userField) {
            if (array_key_exists($field, $data)) {
                $data[$field] = $data[$field] ?: ($user?->$userField);
            }
        }

        if (isset($data['is_negotiable']) && filter_var($data['is_negotiable'], FILTER_VALIDATE_BOOLEAN)) {
            $data['salary_min'] = 0;
            $data['salary_max'] = 0;
        }

        return $data;
    }
}
