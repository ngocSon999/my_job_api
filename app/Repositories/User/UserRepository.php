<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByPhone($phone)
    {
        return $this->model->where('phone', $phone)->first();
    }
}
