<?php

namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\BaseRepository;

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
            ->latest()
            ->paginate(15);
    }
}
