<?php

namespace App\Repositories\Interaction;

use App\Models\Interaction;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class InteractionRepository extends BaseRepository implements InteractionRepositoryInterface
{
    public function __construct(Interaction $model)
    {
        parent::__construct($model);
    }

    public function getAllInteractions($userId, $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['post', 'employer', 'candidate'])
            ->where(function($query) use ($userId) {
                $query->where('candidate_id', $userId)
                    ->orWhere('employer_id', $userId);
            })
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function findExisting($employerId, $postId, $candidateId)
    {
        return $this->model->where('employer_id', $employerId)
            ->where('post_id', $postId)
            ->where('candidate_id', $candidateId)
            ->first();
    }
}
