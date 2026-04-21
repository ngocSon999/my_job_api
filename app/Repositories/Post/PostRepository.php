<?php

namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function getActivePosts(array $data): mixed
    {
        return $this->model->where('status', 'active')
            ->where('user_id', '<>', Auth::id())
            ->latest()
            ->paginate(15);
    }

    public function getMyPosts(array $data): mixed
    {
        return $this->model->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
    }
}
