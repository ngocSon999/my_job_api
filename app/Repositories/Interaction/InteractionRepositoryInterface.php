<?php

namespace App\Repositories\Interaction;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface InteractionRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllInteractions($userId, $perPage = 15): LengthAwarePaginator;
}
