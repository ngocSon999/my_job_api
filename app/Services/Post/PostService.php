<?php

namespace App\Services\Post;

use App\Repositories\Post\PostRepositoryInterface;
use App\Services\BaseService;
use Exception;
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

            $data['contact_name']  = $data['contact_name']  ?? $user->name;
            $data['contact_phone'] = $data['contact_phone'] ?? $user->phone;
            $data['contact_email'] = $data['contact_email'] ?? $user->email;

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

        if ($post->user_id !== Auth::id()) {
            throw new Exception("Bạn không có quyền chỉnh sửa bài viết này.", 403);
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();

            if (array_key_exists('contact_name', $data)) {
                $data['contact_name'] = $data['contact_name'] ?: $user->name;
            }
            if (array_key_exists('contact_phone', $data)) {
                $data['contact_phone'] = $data['contact_phone'] ?: $user->phone;
            }
            if (array_key_exists('contact_email', $data)) {
                $data['contact_email'] = $data['contact_email'] ?: $user->email;
            }

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
}
